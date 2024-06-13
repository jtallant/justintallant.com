import CommentFormHandler from '../../public/js/CommentFormHandler.js';
import { describe, it, beforeEach, expect, vi } from 'vitest';
import { JSDOM } from 'jsdom';

describe('CommentFormHandler', () => {
    let commentFormHandler;
    let form;
    let dom;

    beforeEach(() => {
        dom = new JSDOM(`
            <form id="comment-form">
                <input name="author" value="Test Author">
                <textarea name="content">Test Content</textarea>
                <input type="hidden" name="replies_to_id" value="123">
                <input type="hidden" name="entry_uri" value="test-entry-uri">
                <div class="char-count"><span>2400</span></div>
            </form>
        `);
        const document = dom.window.document;
        form = document.getElementById('comment-form');
        commentFormHandler = new CommentFormHandler(document);

        vi.spyOn(console, 'error').mockImplementation(() => { });
        vi.spyOn(console, 'warn').mockImplementation(() => { });
    });

    it('should initialize and add submit event listener', () => {
        const addEventListenerSpy = vi.spyOn(form, 'addEventListener');
        commentFormHandler.init();
        expect(addEventListenerSpy).toHaveBeenCalledWith('submit', expect.any(Function));
    });

    it('should handle form submission and prevent default action', () => {
        const event = new dom.window.Event('submit');
        const preventDefaultSpy = vi.spyOn(event, 'preventDefault');
        const handleSubmitSpy = vi.spyOn(commentFormHandler, 'handleSubmit');

        commentFormHandler.init();
        form.dispatchEvent(event);

        expect(preventDefaultSpy).toHaveBeenCalled();
        expect(handleSubmitSpy).toHaveBeenCalledWith(event);
    });

    it('should post comment with form data', () => {
        const data = {
            author: form.querySelector('input[name="author"]').value,
            content: form.querySelector('textarea[name="content"]').value,
            replies_to_id: form.querySelector('input[name="replies_to_id"]').value,
            entry_uri: form.querySelector('input[name="entry_uri"]').value
        };

        const fetchSpy = vi.spyOn(global, 'fetch').mockResolvedValue({
            json: () => Promise.resolve({ data: data })
        });

        commentFormHandler.init();

        form.dispatchEvent(new dom.window.Event('submit'));

        expect(fetchSpy).toHaveBeenCalledWith('/api/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        fetchSpy.mockRestore();
    });

    it('should handle response and dispatch custom event', () => {
        const mockForm = {
            addEventListener: vi.fn(),
            querySelector: vi.fn().mockReturnValue({ value: 'test-value' })
        };

        const mockDocument = {
            dispatchEvent: vi.fn(),
            getElementById: vi.fn().mockReturnValue(mockForm)
        };

        const handler = new CommentFormHandler(mockDocument);

        const mockResponse = {
            data: {
                author: 'John Doe',
                content: 'This is a test comment.'
            }
        };

        handler.handleResponse(mockResponse);

        expect(mockDocument.dispatchEvent).toHaveBeenCalledWith(
            new CustomEvent('commentPosted', { detail: mockResponse.data })
        );
    });

    it('should clear form fields', () => {
        commentFormHandler.clearFormFields();

        expect(form.querySelector('input[name="author"]').value).toBe('');
        expect(form.querySelector('textarea[name="content"]').value).toBe('');
        expect(form.querySelector('input[name="replies_to_id"]').value).toBe('');
        expect(form.querySelector('.char-count span').textContent).toBe('2400');
    });
});
