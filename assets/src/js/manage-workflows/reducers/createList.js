import {combineReducers} from 'redux';

/**
 * Reducer for returning workflow IDs from the API response.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {Array}
 */
const ids = (state = [], action) => {

    switch ( action.type ) {

        case 'FETCH_WORKFLOWS_SUCCESS':

            return action.response.result;

        case 'ADD_WORKFLOW_SUCCESS':

            return [
                ...state,
                action.response.result,
            ];

        case 'DELETE_WORKFLOW_SUCCESS':

            return state.filter(id => id !== action.response.id);

        default:

            return state;
    }
};

/**
 * Reducer for determining if the API is fetching.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {boolean}
 */
const isFetching = (state = false, action) => {

    switch ( action.type ) {

        case 'FETCH_WORKFLOWS_REQUEST':

            return true;

        case 'FETCH_WORKFLOWS_SUCCESS':
        case 'FETCH_WORKFLOWS_FAILURE':

            return false;

        default:

            return state;
    }
};

/**
 * Reducer for determining if the API is adding.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {boolean}
 */
const isAdding = (state = false, action) => {

    switch ( action.type ) {

        case 'ADD_WORKFLOW_REQUEST':

            return true;

        case 'ADD_WORKFLOW_SUCCESS':
        case 'ADD_WORKFLOW_FAILURE':

            return false;

        default:

            return state;
    }
};

/**
 * Reducer for determining if the API is editing.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {boolean}
 */
const isEditing = (state = false, action) => {

    switch ( action.type ) {

        case 'EDIT_WORKFLOW_REQUEST':

            return action.id;

        case 'EDIT_WORKFLOW_SUCCESS':
        case 'EDIT_WORKFLOW_FAILURE':

            return false;

        default:

            return state;
    }
};

/**
 * Reducer for determining if the API is deleting.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {boolean}
 */
const isDeleting = (state = false, action) => {

    switch ( action.type ) {

        case 'DELETE_WORKFLOW_REQUEST':

            return action.id;

        case 'DELETE_WORKFLOW_SUCCESS':
        case 'DELETE_WORKFLOW_FAILURE':

            return false;

        default:

            return state;
    }
};

/**
 * Reducer for the error message on API call.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {*}
 */
const errorMessage = (state = null, action) => {

    switch ( action.type ) {

        case 'FETCH_WORKFLOWS_FAILURE':
        case 'ADD_WORKFLOWS_FAILURE':
        case 'EDIT_WORKFLOWS_FAILURE':
        case 'DELETE_WORKFLOWS_FAILURE':

            return action.message;

        case 'FETCH_WORKFLOWS_REQUEST':
        case 'FETCH_WORKFLOWS_SUCCESS':

            return null;

        default:

            return state;
    }
}

/**
 * Reducer for dealing with all workflow IDs.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state to modify.
 * @param {{}} action The current action to use.
 * @returns {*}
 */
const createList = combineReducers({
    ids,
    isFetching,
    isAdding,
    isEditing,
    isDeleting,
    errorMessage,
});

export default createList;

export const getIds          = (state) => state.ids;
export const getIsFetching   = (state) => state.isFetching;
export const getIsAdding     = (state) => state.isAdding;
export const getIsEditing    = (state) => state.isEditing;
export const getIsDeleting   = (state) => state.isDeleting;
export const getErrorMessage = (state) => state.errorMessage;