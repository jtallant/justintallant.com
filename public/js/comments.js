document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const authorName = form.querySelector('input[name="comment_author"]').value;
        const content = form.querySelector('textarea[name="content"]').value;
        const entryUri = form.querySelector('input[name="entry_uri"]').value;
        const parentId = form.querySelector('input[name="parent_id"]').value;

        const data = {
            author: authorName,
            content: content,
            entry_uri: entryUri,
            parent_id: parentId
        };

        fetch('/api/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(response => {
            const data = response.data;
            const commentTemplate = document.querySelector('#comment-template .comment').cloneNode(true);

            commentTemplate.querySelector('.author-name').textContent = data.author;
            commentTemplate.querySelector('.comment-content p').innerHTML = data.content;

            if (data.is_author) {
                commentTemplate.querySelector('.author-author-img').style.display = 'block';
                commentTemplate.querySelector('.author-non-author-img').style.display = 'none';
            } else {
                commentTemplate.querySelector('.author-author-img').style.display = 'none';
                commentTemplate.querySelector('.author-non-author-img').style.display = 'block';
            }

            const commentsList = document.querySelector('.comments-list');

            if (data.parent_id) {
                const parentComment = commentsList.querySelector(`.comment[data-comment-id="${data.parent_id}"]`);
                if (parentComment) {
                    const repliesDiv = parentComment.querySelector('.replies');
                    repliesDiv.insertBefore(commentTemplate, repliesDiv.firstChild);
                }
            } else {
                commentsList.insertBefore(commentTemplate, commentsList.firstChild);
            }

            // Clear the form fields
            form.querySelector('input[name="comment_author"]').value = '';
            form.querySelector('textarea[name="content"]').value = '';
            form.querySelector('input[name="parent_id"]').value = '';
            form.querySelector('.char-count span').textContent = '2400';
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
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
        const replyToInput = commentForm.querySelector('input[name="parent_id"]');

        commentForm.style.display = 'block';

        textarea.value = `@${commentAuthor} `;
        replyToInput.value = commentId;
        textarea.focus();

        window.scrollTo({
            top: commentForm.getBoundingClientRect().top + window.scrollY - 100,
            behavior: 'smooth'
        });
    }
});
