MathJax = {
    tex: {
        inlineMath: [['$', '$']],
        processEscapes: true,
        macros: {
            llbracket: '\\unicode{10214}',
            rrbracket: '\\unicode{10215}',
            rg: '\\text{rg}',
            dim: '\\text{dim}',
            sup: '\\text{sup}',
            inf: '\\text{inf}',
            et: '\\text{ et }'
        }
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'textarea', 'pre']
    }
};

// Fonction pour initialiser la prévisualisation LaTeX
const initLatexPreview = (input) => {
    if (!input) return;

    const id = input.id || `latex-preview-${Math.random().toString(36).substr(2, 9)}`;
    input.id = id;

    let previewContainer = input.nextElementSibling;
    if (!previewContainer || !previewContainer.classList.contains('latex-preview-container')) {
        previewContainer = document.createElement('div');
        previewContainer.className = 'latex-preview-container mt-2';
        previewContainer.innerHTML = `
                <div class="text-sm text-text-300">Prévisualisation :</div>
                <div class="latex-preview p-4 bg-foreground-400 rounded" data-preview-for="${id}"></div>
            `;
        input.parentNode.insertBefore(previewContainer, input.nextSibling);
    }

    const preview = previewContainer.querySelector('.latex-preview');

    const render = () => {
        preview.innerHTML = input.value;
        if (window.MathJax && MathJax.typesetPromise) {
            MathJax.typesetPromise([preview]);
        }
    };

    input.addEventListener('input', render);
    render();
};

document.addEventListener('DOMContentLoaded', function () {
    // Initialisation des prévisualisations existantes
    document.querySelectorAll('.latex-input').forEach(initLatexPreview);
});

// Initialisation automatique des latex preview ajoutées et les latex tout court
const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        for (const addedNode of mutation.addedNodes) {
            if (addedNode.nodeType !== Node.ELEMENT_NODE) continue;

            addedNode.querySelectorAll('.latex-input').forEach(initLatexPreview);
            MathJax.typesetPromise(addedNode.querySelectorAll('.latex'));
        }
    }
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});
