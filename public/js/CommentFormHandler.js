export default class CommentFormHandler {
    constructor() {
        this.form = document.getElementById('comment-form');
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

        document.dispatchEvent(
            new CustomEvent('commentPosted', { detail: data })
        );

        this.clearFormFields();
    }

    clearFormFields() {
        this.form.querySelector('input[name="author"]').value = '';
        this.form.querySelector('textarea[name="content"]').value = '';
        this.form.querySelector('input[name="parent_id"]').value = '';
        // move to different class
        // this.form.querySelector('.char-count span').textContent = '2400';
    }
}