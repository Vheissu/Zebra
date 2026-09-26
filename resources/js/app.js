/*
 * Zebra works without JavaScript: every vote, reply and delete is a plain
 * form. This file makes those forms quicker to use: votes happen in place,
 * replies open under the comment, and threads can be collapsed.
 */

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
const toast = document.querySelector('.toast');
let toastTimer;

function say(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => (toast.hidden = true), 4000);
}

/* ---------- Voting ---------- */

const downvoteDialog = document.getElementById('downvote-dialog');

/**
 * Open a <dialog> whose form uses method="dialog" and resolve with the value
 * of the button that closed it, or null if it was cancelled.
 */
function ask(dialog, prepare = () => {}) {
    return new Promise((resolve) => {
        const form = dialog.querySelector('form');
        prepare(dialog);

        const finish = (value) => {
            form.removeEventListener('submit', onSubmit);
            dialog.removeEventListener('cancel', onCancel);
            resolve(value);
        };
        const onSubmit = (event) => finish(event.submitter?.value === 'confirm' ? 'confirm' : null);
        const onCancel = () => finish(null);

        form.addEventListener('submit', onSubmit);
        dialog.addEventListener('cancel', onCancel);
        dialog.showModal();
    });
}

async function askForReason() {
    const select = downvoteDialog.querySelector('select');
    const answer = await ask(downvoteDialog, () => (select.value = ''));

    return answer === 'confirm' ? select.value : null;
}

function setVoteState(box, value) {
    box.dataset.vote = String(value);

    for (const button of box.querySelectorAll('[data-direction]')) {
        const mine = button.dataset.direction === 'up' ? 1 : -1;
        const pressed = value === mine;
        button.setAttribute('aria-pressed', String(pressed));
        button.form.elements.direction.value = pressed ? 'none' : button.dataset.direction;
    }
}

document.addEventListener('submit', async (event) => {
    const button = event.submitter;
    const box = button?.closest('.vote');
    if (!box) return;

    event.preventDefault();
    if (box.classList.contains('is-busy')) return;

    const form = button.form;
    const direction = form.elements.direction.value;
    const body = new FormData(form);

    if (direction === 'down') {
        const reason = await askForReason();
        if (!reason) return;
        body.set('reason', reason);
    }

    box.classList.add('is-busy');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            body,
        });
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const firstError = data.errors && Object.values(data.errors)[0]?.[0];
            say(firstError || data.message || 'That vote didn’t go through. Try again.');
            return;
        }

        setVoteState(box, { up: 1, down: -1, none: 0 }[data.vote]);

        for (const score of document.querySelectorAll(`[data-score-for="${box.dataset.item}"]`)) {
            score.textContent = data.score;
            const unit = score.nextSibling;
            if (unit?.nodeType === Node.TEXT_NODE) {
                unit.textContent = unit.textContent.replace(/points?/, Math.abs(data.score) === 1 ? 'point' : 'points');
            }
        }
    } catch {
        say('Couldn’t reach the server. Check your connection.');
    } finally {
        box.classList.remove('is-busy');
    }
});

/* ---------- Confirming destructive actions ---------- */

const confirmDialog = document.createElement('dialog');
confirmDialog.innerHTML = `
    <form method="dialog">
        <h2></h2>
        <div class="actions">
            <button type="submit" value="confirm" class="button button--danger">Yes, do it</button>
            <button type="submit" value="cancel" class="button button--quiet">Cancel</button>
        </div>
    </form>`;
document.body.append(confirmDialog);

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!form.dataset.confirm || form.dataset.confirmed) return;

    event.preventDefault();
    const answer = await ask(confirmDialog, (dialog) => (dialog.querySelector('h2').textContent = form.dataset.confirm));

    if (answer === 'confirm') {
        form.dataset.confirmed = 'yes';
        form.requestSubmit(event.submitter);
    }
});

/* ---------- Threads ---------- */

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-toggle]');
    if (toggle) {
        const comment = toggle.closest('.comment');
        const collapsed = comment.classList.toggle('is-collapsed');
        const hidden = comment.querySelectorAll('.comment').length;
        toggle.setAttribute('aria-expanded', String(!collapsed));
        toggle.textContent = collapsed ? `[+${hidden}]` : '[–]';
        toggle.title = collapsed ? 'Expand thread' : 'Collapse thread';
        return;
    }

    const reply = event.target.closest('[data-reply]');
    const template = document.getElementById('reply-template');
    if (reply && template) {
        event.preventDefault();

        const comment = reply.closest('.comment');
        const slot = comment.querySelector(':scope > .reply-slot');
        const open = slot.querySelector('form');
        if (open) {
            open.querySelector('textarea').focus();
            return;
        }

        const form = template.content.firstElementChild.cloneNode(true);
        const id = `reply-body-${comment.dataset.comment}`;
        form.elements.parent_id.value = comment.dataset.comment;
        form.querySelector('textarea').id = id;
        form.querySelector('label').htmlFor = id;
        slot.append(form);
        form.querySelector('textarea').focus();
        return;
    }

    const cancel = event.target.closest('[data-cancel-reply]');
    if (cancel) {
        cancel.closest('form').remove();
    }
});
