/**
 * Action creator for opening the modal.
 *
 * @since {{VERSION}}
 *
 * @param {int} id ID for the workflow to edit. Leave empty or enter 0 to create a new workflow.
 * @param {{}} fields Field with which to populate the modal.
 */
export const openWorkflowModal = (id = -1, fields = {}) => ({
    type: 'OPEN_WORKFLOWMODAL',
    id,
    fields,
});

/**
 * Action creator for closing the modal.
 *
 * @since {{VERSION}}
 */
export const closeWorkflowModal = () => ({
    type: 'CLOSE_WORKFLOWMODAL',
});