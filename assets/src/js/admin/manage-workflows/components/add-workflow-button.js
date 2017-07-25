import React from 'react';

/**
 * Presentational component for the add workflow button.
 *
 * @since {{VERSION}}
 *
 * @param children
 * @param handleClick
 * @constructor
 */
const AddWorkflowButton = ({
    children,
    handleClick,
}) => (
    <button
        className="wfm-add-workflow-button page-title-action"
        onClick={handleClick}
    >
        {children}
    </button>
)

export default AddWorkflowButton;