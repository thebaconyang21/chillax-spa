document.addEventListener('click', (event) => {
    const editButton = event.target.closest('[data-confirm-edit]');

    if (!editButton) {
        return;
    }

    const message = editButton.dataset.confirmMessage
        || (editButton.dataset.recordName
            ? `Are you sure you want to edit "${editButton.dataset.recordName}"?`
            : 'Are you sure you want to edit this record?');

    if (!window.confirm(message)) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm-delete]');

    if (!form) {
        return;
    }

    const message = form.dataset.confirmMessage
        || (form.dataset.recordName
            ? `Are you sure you want to delete "${form.dataset.recordName}"? This action cannot be undone.`
            : 'Are you sure you want to delete this record? This action cannot be undone.');

    if (!window.confirm(message)) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);
