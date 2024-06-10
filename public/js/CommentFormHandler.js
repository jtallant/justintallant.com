export default class CommentFormHandler {
    constructor(domDoc) {
        this.domDoc = domDoc;
        this.form = domDoc.getElementById('comment-form');
    }

    init() {
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    handleSubmit(event) {
        event.preventDefault();
        const formElements = event.target.elements;

        const data = {
            author: formElements['author'].value,
            content: formElements['content'].value,
            parent_id: formElements['parent_id'].value,
            entry_uri: formElements['entry_uri'].value
        }

        this.postComment(data);
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

        this.domDoc.dispatchEvent(
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