import React from 'react';

/**
 * Presentational component for the Workflow edit/add modal.
 *
 * @since {{VERSION}}
 *
 * @param title
 * @returns {XML}
 * @constructor
 */
const Modal = ({
    title,
    submitText,
    children,
    handleClose,
    handleSubmit,
    handleOnChange,
}) => (
    <div className="wfm-modal">
        <div className="wfm-modal-container">
            <header className="wfm-modal-header">
                <h1 className="wfm-modal-title">
                    {title}
                </h1>

                <a href="#" className="wfm-modal-close" onClick={handleClose}>
                    <span className="dashicons dashicons-no"/>
                </a>
            </header>

            <div className="wfm-modal-body">
                {children}
            </div>

            <footer className="wfm-modal-footer">
                <button type="button"
                        className="wfm-modal-submit button button-primary"
                        onClick={handleSubmit}
                >
                    {submitText}
                </button>
            </footer>
        </div>
    </div>
);

export default Modal;