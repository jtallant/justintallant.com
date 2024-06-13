import CommentFormHandler from './CommentFormHandler.js';
import CommentInserter from './CommentInserter.js';

document.addEventListener('DOMContentLoaded', () => {
    new CommentFormHandler(document).init();
    new CommentInserter(document).init();

    const commentFormTextarea = document.querySelector('.comment-form textarea');
    const newCommentLink = document.getElementById('btn-new-comment');
    const commentsList = document.querySelector('.comments-list');
    const verifyEmailForm = document.getElementById('verify-email-form');

    commentFormTextarea.addEventListener('input', updateCharCount);
    verifyEmailForm.addEventListener('submit', handleEmailVerification);

    newCommentLink.addEventListener('click', toggleCommentFormDisplay);

    commentsList.querySelectorAll('.reply-button').forEach(replyButton => {
        replyButton.addEventListener('click', toggleCommentFormDisplay);
    });

    function handleEmailVerification(event) {
        event.preventDefault();
        const email = document.getElementById('email').value;
        const name = document.getElementById('name').value;
        const entryUri = document.querySelector('input[name="entry_uri"]').value;

        fetch('/api/comments/send-email-verification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email, name: name, entry_uri: entryUri }),
        })
            .then(response => response.json())
            .then(data => {
                const form = document.getElementById('verify-email');
                const successMessage = form.querySelector('.success');
                successMessage.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                const form = document.getElementById('verify-email');
                const errorMessage = form.querySelector('.error');
                errorMessage.style.display = 'block';
            });
    }

    function updateCharCount() {
        const maxChars = 2400;
        const currentLength = this.value.length;
        const remainingChars = maxChars - currentLength;
        document.querySelector('.char-count span').textContent = remainingChars;
    }

    function toggleCommentFormDisplay(event) {
        const verifyEmailContainer = document.getElementById('verify-email');
        const commentToken = localStorage.getItem('commentToken');

        if (!commentToken) {
            verifyEmailContainer.classList.add('show-animate');
            verifyEmailContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            console.log('no token');
            return;
        }

        verifyCommentToken(commentToken).then(data => {
            showCommentForm(verifyEmailContainer, event);
        })
        .catch(error => {
            // console.log(error);
        });
    }

    function showCommentForm(verifyEmailContainer, event) {
        const commentName = localStorage.getItem('commentName');
        const isReplyButtonClick = event.target.closest('.reply-button') !== null;
        const commentForm = document.getElementById('comment-form');

        verifyEmailContainer.classList.remove('show-animate');
        commentForm.classList.add('show-animate');
        document.getElementById('comment-author').value = commentName;

        if (isReplyButtonClick) {
            const commentReplyingTo = event.target.closest('.comment');
            setReplyFormFields(commentForm, commentReplyingTo);
        }

        commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setTimeout(() => {
            document.getElementById('comment-content').focus();
        }, 500);
    }

    function setReplyFormFields(commentForm, commentReplyingTo) {
        const repliesTo = commentForm.querySelector('input[name="replies_to_id"]');
        const replyingToAuthor = commentReplyingTo.querySelector('.author-name').textContent;

        repliesTo.value = commentReplyingTo.getAttribute('data-root-comment-id');
        document.getElementById('comment-content').value = `@${replyingToAuthor} `;
    }

    function verifyCommentToken(commentToken) {
        return fetch('/api/comments/verify-comments-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token: commentToken }),
        })
        .then(response => response.json());
    }
});