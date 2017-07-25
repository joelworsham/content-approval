import {combineReducers} from 'redux';
import byId, * as fromById from './byId';
import createList, * as fromList from './createList';

/**
 * Main workflows reducer.
 *
 * @since {{VERSION}}
 *
 * @type {Reducer<S>}
 */
const workflows = combineReducers({
    byId,
    createList,
});

export default workflows;

/**
 * Returns all workflows in an array from the current state.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getWorkflows = (state) => {

    const ids = fromList.getIds(state.createList);
    return ids.map(id => fromById.getWorkflow(state.byId, id));
};

/**
 * Returns if the API is currently fetching.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getIsFetching = (state) => (
    fromList.getIsFetching(state.createList)
);

/**
 * Returns if the API is currently adding.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getIsAdding = (state) => (
    fromList.getIsAdding(state.createList)
);

/**
 * Returns if the API is currently editing.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getIsEditing = (state) => (
    fromList.getIsEditing(state.createList)
);

/**
 * Returns if the API is currently deleting.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getIsDeleting = (state) => (
    fromList.getIsDeleting(state.createList)
);

/**
 * Returns the API error message, if any.
 *
 * @since {{VERSION}}
 *
 * @param {{}} state The current state.
 */
export const getErrorMessage = (state) => (
    fromList.getErrorMessage(state.createList)
);