import CommentFormHandler from './CommentFormHandler.js';
import CommentInserter from './CommentInserter.js';

document.addEventListener('DOMContentLoaded', () => {
    new CommentFormHandler(document).init();
    new CommentInserter(document).init();

    const commentFormTextarea = document.querySelector('.comment-form textarea');
    const newCommentLink = document.querySelector('.new-comment a');
    const commentsList = document.querySelector('.comments-list');
    const verifyEmailForm = document.getElementById('verify-email-form');

    commentFormTextarea.addEventListener('input', updateCharCount);
    newCommentLink.addEventListener('click', toggleCommentFormDisplay);
    commentsList.addEventListener('click', handleReplyButtonClick);
    verifyEmailForm.addEventListener('submit', handleEmailVerification);


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

    document.getElementById('verify-email-form').addEventListener('submit', handleEmailVerification);

    function updateCharCount() {
        const maxChars = 2400;
        const currentLength = this.value.length;
        const remainingChars = maxChars - currentLength;
        document.querySelector('.char-count span').textContent = remainingChars;
    }

    function toggleCommentFormDisplay() {
        const commentForm = document.querySelector('.comment-form');
        const verifyEmailContainer = document.getElementById('verify-email');

        const commentToken = localStorage.getItem('commentToken');
        const commentName = localStorage.getItem('commentName');

        if (!commentToken) {
            verifyEmailContainer.classList.add('show-animate');
            return;
        }

        verifyCommentToken(commentToken).then(data => {
            verifyEmailContainer.classList.remove('show-animate');
            commentForm.classList.add('show-animate');
            document.getElementById('comment_author').value = commentName;

            setTimeout(() => {
                document.getElementById('comment_content').focus();
            }, 600);
        })
        .catch(error => {
            // console.log(error);
        });
    }

    function handleReplyButtonClick(event) {
        if (event.target.closest('a.reply-button')) {

            event.preventDefault();
            const commentForm = document.getElementById('comment-form');
            const commentReplyingTo = event.target.closest('.comment');

            const replyingToAuthor = commentReplyingTo.querySelector('.author-name').textContent;

            const textarea = commentForm.querySelector('textarea[name="content"]');
            textarea.value = `@${replyingToAuthor} `;

            const repliesTo = commentForm.querySelector('input[name="replies_to_id"]');

            commentForm.style.display = 'block';
            repliesTo.value = commentReplyingTo.getAttribute('data-root-comment-id');

            commentForm.scrollIntoView({ behavior: 'smooth', block: 'center', duration: 1200 });

            setTimeout(() => {
                textarea.focus();
            }, 1200);
        }
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