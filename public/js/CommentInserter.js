export default class CommentInserter {
    constructor(domDoc) {
        this.domDoc = domDoc;
        this.commentTemplate = domDoc.getElementById('comment-template').content;
    }

    init() {
        this.domDoc.addEventListener('commentPosted', (event) => {
            this.insertComment(event.detail);
        });
    }

    insertComment(data) {
        const commentClone = document.importNode(this.commentTemplate, true);
        this.populateComment(commentClone, data);
        this.addCommentToDOM(commentClone, data);
    }

    populateComment(commentClone, data) {
        const wrapper = commentClone.querySelector('.comment');
        wrapper.setAttribute('data-comment-id', data.id);
        wrapper.setAttribute('data-root-comment-id', data.root_comment_id);
        wrapper.setAttribute('id', 'comments-' + data.id);

        commentClone.querySelector('.author-name').textContent = data.author;
        commentClone.querySelector('.comment-content').innerHTML = data.content;
        commentClone.querySelector('time').textContent = data.created_at;

        if (data.image_html) {
            const commentAuthorDiv = commentClone.querySelector('.comment-author');
            commentAuthorDiv.insertAdjacentHTML('afterbegin', data.image_html);
        }
    }

    addCommentToDOM(commentClone, data) {
        const commentsList = this.domDoc.querySelector('.comments-list');

        if (data.replies_to_id) {
            this.insertReply(data, commentClone, commentsList);
        } else {
            this.insertNewComment(commentClone, commentsList);
        }

        const insertedComment = document.getElementById('comments-' + data.id);

        insertedComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
        insertedComment.classList.add('comment-animate');
    }

    insertNewComment(commentClone, commentsList) {
        commentsList.insertBefore(commentClone, commentsList.firstChild);
    }

    insertReply(data, commentClone, commentsList) {
        const parentComment = commentsList.querySelector(`.comment[data-comment-id="${data.replies_to_id}"]`);
        if (parentComment) {
            const repliesDiv = parentComment.querySelector('.replies');
            repliesDiv.appendChild(commentClone);
        }
    }
}