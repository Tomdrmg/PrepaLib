<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EnglishTranslationService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function askCorrection(string $niveauLangue, bool $autorisationContractions, string $texteFrancais, string $texteAnglais): string
    {
        // Construction du prompt système avec vos paramètres
        $niveauLangueInstruction = '';
        if ($niveauLangue === 'soutenue') {
            $niveauLangueInstruction = "\n- Niveau de langue : SOUTENU (vocabulaire recherché, tournures élégantes)";
        } elseif ($niveauLangue === 'normal') {
            $niveauLangueInstruction = "\n- Niveau de langue : STANDARD (registre courant, ni familier ni soutenu)";
        }

        $contractionsInstruction = '';
        if ($autorisationContractions === false) {
            $contractionsInstruction = "\n- INTERDICTION STRICTE des contractions anglaises (utilise 'do not' jamais 'don't', 'cannot' jamais 'can't', etc.)";
        }

        $style = $contractionsInstruction == '' && $niveauLangueInstruction == '' ? '' : "CONTRAINTES DE STYLE :" . $niveauLangueInstruction . $contractionsInstruction . "\n\n";

        $promptSysteme = "Tu es un professeur d'anglais francophone expert en correction de traductions.

Je vais te fournir un texte original en français et une traduction anglaise produite par un étudiant.

MISSION :
Analyser la traduction et renvoyer UNIQUEMENT un fichier CSV listant les erreurs détectées.

".$style."RÈGLE DE PONCTUATION :
- Utilise UNIQUEMENT le caractère ' (apostrophe droite U+0027) pour les apostrophes.
  N'utilise JAMAIS ` (backtick) ni ' (guillemet courbe).

TYPES D'ERREURS AUTORISÉS :
conjugaison, contexte, accord, syntaxe, mieux, vocabulaire, orthographe, ponctuation, préposition

FORMAT DE SORTIE :
CSV avec en-têtes, séparateur virgule, pas de markdown.

COLONNES (dans cet ordre) :
1. type - un des types listés ci-dessus
2. original_segment - copie EXACTE du passage erroné (casse, espaces, ponctuation)
3. correction - la correction proposée
4. explanation - explication pédagogique courte en français
5. approx_position - position approximative du premier caractère du segment dans le texte anglais

RÈGLES CRITIQUES :
- Relève TOUTES les erreurs présentes dans la traduction sans exception
- Ne crée AUCUNE erreur artificielle : corrige uniquement ce qui est réellement problématique
- Chaque ligne = une erreur
- Si la traduction est parfaite, renvoie uniquement la ligne d'en-têtes
- Échappe les guillemets dans les cellules CSV (doubler les \")";

        $promptUtilisateur = "Texte original (FR) :
\"\"\"$texteFrancais\"\"\"

Traduction étudiant (EN) :
\"\"\"$texteAnglais\"\"\"";

        // Appel API avec messages séparés système/utilisateur
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-5-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $promptSysteme],
                    ['role' => 'user', 'content' => $promptUtilisateur],
                ],
                'temperature' => 0.3,  // Plus bas pour plus de cohérence (optionnel)
            ],
        ]);

        // Récupération du CSV
        $body = json_decode($response->getContent(), true);
        $csvContent = $body['choices'][0]['message']['content'];

        // Parser le CSV
        $lines = str_getcsv($csvContent, "\n");
        $errors = [];
        for ($i = 1; $i < count($lines); $i++) { // skip header
            $fields = str_getcsv($lines[$i]);
            $errors[] = [
                'type' => $fields[0],
                'original_segment' => $fields[1],
                'correction' => $fields[2],
                'explanation' => $fields[3],
                'approx_position' => (int)$fields[4],
            ];
        }

        return $data['choices'][0]['message']['content'] ?? '';
    }
}
