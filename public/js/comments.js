import CommentFormHandler from './CommentFormHandler.js';
import CommentInserter from './CommentInserter.js';

document.addEventListener('DOMContentLoaded', () => {
    new CommentFormHandler(document).init();
    new CommentInserter(document).init();
});

document.querySelector('.comment-form textarea').addEventListener('input', function () {
    const maxChars = 2400;
    const currentLength = this.value.length;
    const remainingChars = maxChars - currentLength;
    document.querySelector('.char-count span').textContent = remainingChars;
});

document.querySelector('.new-comment a').addEventListener('click', function () {
    const commentForm = document.querySelector('.comment-form');
    const current = commentForm.style.display;
    commentForm.style.display = (current === 'none' || current === '') ? 'block' : 'none';
});

document.querySelector('.comments-list').addEventListener('click', function (event) {
    if (event.target.classList.contains('reply-button')) {
        const commentElement = event.target.closest('.comment');
        const commentAuthor = commentElement.querySelector('.author-name').textContent;
        const commentId = commentElement.getAttribute('data-comment-id');
        const commentForm = document.querySelector('.comment-form');
        const textarea = commentForm.querySelector('textarea[name="content"]');
        const parentIdInput = commentForm.querySelector('input[name="parent_id"]');

        commentForm.style.display = 'block';

        textarea.value = `@${commentAuthor} `;
        parentIdInput.value = commentId;
        textarea.focus();

        window.scrollTo({
            top: commentForm.getBoundingClientRect().top + window.scrollY - 100,
            behavior: 'smooth'
        });
    }
});

