/**
 * Reducer for workflows stored by their unique IDs.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {[]}
 */
const byId = (state = {}, action) => {

    switch (action.type) {

        case 'FETCH_WORKFLOWS_SUCCESS':
        case 'ADD_WORKFLOW_SUCCESS':

            return {
                ...state,
                ...action.response.entities.workflows,
            };

        case 'EDIT_WORKFLOW_SUCCESS':

            return {
                ...state,
                [action.response.result]: action.response.entities.workflows[action.response.result],
            }

        case 'DELETE_WORKFLOW_SUCCESS':

            const rawState = {...state};

            delete rawState[action.response.id];

            return rawState;

        default:

            return state;
    }
};

export default byId;

export const getWorkflow = (state, id) => state[id];