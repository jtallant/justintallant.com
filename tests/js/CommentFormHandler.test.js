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
                <input name="parent_id" value="123">
                <input name="entry_uri" value="test-entry-uri">
            </form>
        `);
        const document = dom.window.document;
        form = document.getElementById('comment-form');
        commentFormHandler = new CommentFormHandler(document);
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
        const fetchSpy = vi.spyOn(global, 'fetch').mockResolvedValue({
            json: () => Promise.resolve({ data: { author: 'Test Author', content: 'Test Content', parent_id: '123', entry_uri: 'test-entry-uri' } })
        });

        const expectedData = {
            author: form.querySelector('input[name="author"]').value,
            content: form.querySelector('textarea[name="content"]').value,
            parent_id: form.querySelector('input[name="parent_id"]').value,
            entry_uri: form.querySelector('input[name="entry_uri"]').value
        };

        commentFormHandler.init();
        form.dispatchEvent(new dom.window.Event('submit'));

        expect(fetchSpy).toHaveBeenCalledWith('/api/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(expectedData)
        });

        fetchSpy.mockRestore();
    });

    it('should handle response and dispatch custom event', () => {
        // Mock the document and its methods
        const mockForm = {
            addEventListener: vi.fn(),
            querySelector: vi.fn().mockReturnValue({ value: 'test-value' })
        };

        const mockDocument = {
            dispatchEvent: vi.fn(),
            getElementById: vi.fn().mockReturnValue(mockForm)
        };

        // Create an instance of the class with the mocked document
        const handler = new CommentFormHandler(mockDocument);

        // Mock response data
        const mockResponse = {
            data: {
                author: 'John Doe',
                content: 'This is a test comment.'
            }
        };

        // Call the method under test
        handler.handleResponse(mockResponse);

        // Assert that dispatchEvent was called correctly
        expect(mockDocument.dispatchEvent).toHaveBeenCalledWith(
            new CustomEvent('commentPosted', { detail: mockResponse.data })
        );
    });

    it('should clear form fields', () => {
        commentFormHandler.clearFormFields();

        expect(form.querySelector('input[name="author"]').value).toBe('');
        expect(form.querySelector('textarea[name="content"]').value).toBe('');
        expect(form.querySelector('input[name="parent_id"]').value).toBe('');
    });
});
