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
        .then(data => {
            console.log('Success:', data);
            // Put the comment on the page
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
