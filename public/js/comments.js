import CommentFormHandler from './CommentFormHandler.js';
import CommentInserter from './CommentInserter.js';

document.addEventListener('DOMContentLoaded', () => {
    new CommentFormHandler(document).init();
    new CommentInserter(document).init();

    const commentFormTextarea = document.querySelector('.comment-form textarea');
    const newCommentLink = document.querySelector('.new-comment a');
    const commentsList = document.querySelector('.comments-list');

    commentFormTextarea.addEventListener('input', updateCharCount);
    newCommentLink.addEventListener('click', toggleCommentFormDisplay);
    commentsList.addEventListener('click', handleReplyButtonClick);

    function updateCharCount() {
        const maxChars = 2400;
        const currentLength = this.value.length;
        const remainingChars = maxChars - currentLength;
        document.querySelector('.char-count span').textContent = remainingChars;
    }

    function toggleCommentFormDisplay() {
        const commentForm = document.querySelector('.comment-form');
        const current = commentForm.style.display;
        commentForm.style.display = (current === 'none' || current === '') ? 'block' : 'none';
    }

    function handleReplyButtonClick(event) {
        if (event.target.closest('a.reply-button')) {
            event.preventDefault();
            const commentElement = event.target.closest('.comment');
            const commentAuthor = commentElement.querySelector('.author-name').textContent;
            const commentId = commentElement.getAttribute('data-comment-id');
            const commentForm = document.querySelector('.comment-form');
            const textarea = commentForm.querySelector('textarea[name="content"]');
            const repliesToIdInput = commentForm.querySelector('input[name="replies_to_id"]');

            commentForm.style.display = 'block';

            commentForm.scrollIntoView({ behavior: 'smooth', block: 'center', duration: 1200 });

            setTimeout(() => {
                textarea.value = `@${commentAuthor} `;
                repliesToIdInput.value = commentId;
                textarea.focus();
            }, 1200);
        }
    }
});