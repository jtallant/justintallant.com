document.getElementById('comment-form').addEventListener('submit', function (event) {
    event.preventDefault();

    console.log('submit');

    return;

    const authorName = document.querySelector('input[name="comment_author"]').value;
    const content = document.getElementById('content').value;
    const entryUri = document.getElementById('entry_uri').value;

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
        // Optionally, you can add code here to update the comments section with the new comment
    })
    .catch((error) => {
        console.error('Error:', error);
    });
});

document.querySelector('.comment-form textarea').addEventListener('input', function () {
    const maxChars = 2400;
    const currentLength = this.value.length;
    const remainingChars = maxChars - currentLength;
    document.querySelector('.char-count span').textContent = remainingChars;
});
