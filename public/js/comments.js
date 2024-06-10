document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const authorName = form.querySelector('input[name="comment_author"]').value;
        const content = form.querySelector('textarea[name="content"]').value;
        const entryUri = form.querySelector('input[name="entry_uri"]').value;

        const data = {
            author: authorName,
            content: content,
            entry_uri: entryUri
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
            commentsList.insertBefore(commentTemplate, commentsList.firstChild);

            // Clear the form fields
            form.querySelector('input[name="comment_author"]').value = '';
            form.querySelector('textarea[name="content"]').value = '';
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
