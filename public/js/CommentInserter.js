export default class CommentInserter {
    constructor(domDoc) {
        this.domDoc = domDoc;
        this.commentTemplate = domDoc.getElementById('comment-template');
    }

    init() {
        this.domDoc.addEventListener('commentPosted', (event) => {
            this.insertComment(event.detail);
        });
    }

    insertComment(data) {
        const commentClone = this.commentTemplate.querySelector('.comment').cloneNode(true);
        this.populateComment(commentClone, data);
        this.addCommentToDOM(commentClone, data);
    }

    populateComment(commentClone, data) {
        commentClone.querySelector('.author-name').textContent = data.author;
        commentClone.querySelector('.comment-content p').innerHTML = data.content;
        commentClone.setAttribute('data-comment-id', data.id);
        this.toggleAuthorImage(commentClone, data.is_author);
    }

    toggleAuthorImage(commentClone, isAuthor) {
        if (isAuthor) {
            commentClone.querySelector('.author-author-img').style.display = 'block';
            commentClone.querySelector('.author-non-author-img').style.display = 'none';
        } else {
            commentClone.querySelector('.author-author-img').style.display = 'none';
            commentClone.querySelector('.author-non-author-img').style.display = 'block';
        }
    }

    addCommentToDOM(commentClone, data) {
        const commentsList = this.domDoc.querySelector('.comments-list');

        if (data.parent_id) {
            this.insertReply(data, commentClone, commentsList);
        } else {
            this.insertNewComment(commentClone, commentsList);
        }
    }

    insertNewComment(commentClone, commentsList) {
        commentsList.insertBefore(commentClone, commentsList.firstChild);
    }

    insertReply(data, commentClone, commentsList) {
        const parentComment = commentsList.querySelector(`.comment[data-comment-id="${data.parent_id}"]`);
        if (parentComment) {
            const repliesDiv = parentComment.querySelector('.replies');
            repliesDiv.appendChild(commentClone);
        }
    }
}

