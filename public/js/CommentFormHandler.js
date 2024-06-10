export default class CommentFormHandler {
    constructor() {
        this.form = document.getElementById('comment-form');
        this.commentTemplate = document.getElementById('comment-template');
    }

    init() {
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    handleSubmit(event) {
        event.preventDefault();
        const formData = new FormData(this.form);
        this.postComment(Object.fromEntries(formData.entries()));
    }

    postComment(data) {
        fetch('/api/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(response => this.handleResponse(response))
            .catch((error) => {
                console.error('Error:', error);
            });
    }

    handleResponse(response) {
        const data = response.data;
        const commentClone = this.commentTemplate.querySelector('.comment').cloneNode(true);

        this.populateComment(commentClone, data);
        this.insertComment(commentClone, data);
        this.clearFormFields();
    }

    insertComment(commentClone, data) {
        const commentsList = document.querySelector('.comments-list');

        if (data.parent_id) {
            this.insertReply(data, commentClone, commentsList);
        } else {
            this.insertNewComment(commentClone, commentsList);
        }
    }

    populateComment(commentClone, data) {
        commentClone.querySelector('.author-name').textContent = data.author;
        commentClone.querySelector('.comment-content p').innerHTML = data.content;
        this.toggleAuthorImage(commentClone, data.is_author);
    }

    toggleAuthorImage(commentClone, isAuthor) {
        if (isAuthor) {
            commentClone.querySelector('.author-author-img').style.display = 'block';
            commentClone.querySelector('.author-non-author-img').style.display = 'none';
            return;
        }

        commentClone.querySelector('.author-author-img').style.display = 'none';
        commentClone.querySelector('.author-non-author-img').style.display = 'block';
    }

    insertNewComment(commentClone, commentsList) {
        commentsList.insertBefore(commentClone, commentsList.firstChild);
    }

    insertReply(data, commentClone, commentsList) {
        if (data.parent_id) {
            const parentComment = commentsList.querySelector(`.comment[data-comment-id="${data.parent_id}"]`);
            if (parentComment) {
                const repliesDiv = parentComment.querySelector('.replies');
                repliesDiv.insertBefore(commentClone, repliesDiv.firstChild);
            }
        }
    }

    clearFormFields() {
        this.form.querySelector('input[name="author"]').value = '';
        this.form.querySelector('textarea[name="content"]').value = '';
        this.form.querySelector('input[name="parent_id"]').value = '';
        this.form.querySelector('.char-count span').textContent = '2400';
    }
}