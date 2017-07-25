/**
 * Reducer for the workflow modal.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 *
 * @returns {{}}
 */
const workflowModal = (state = {
    open: false,
    id: null,
    fields: {},
}, action) => {

    switch (action.type) {

        case 'OPEN_WORKFLOWMODAL':

            return {
                ...state,
                open: true,
                id: action.id,
                fields: action.fields,
            }

        case 'CLOSE_WORKFLOWMODAL':

            return {
                ...state,
                open: false,
            }

        default:

            return state;
    }
}

export default workflowModal;