import {normalize} from 'normalizr';
import * as schema from './schema';
import * as api from '../api';
import * as workflows from '../reducers/workflows';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Action creator for asynchronously fetching workflows.
 *
 * @since {{VERSION}}
 */
export const fetchWorkflows = () => (dispatch, getState) => {

    if ( workflows.getIsFetching(getState().workflows) ) {

        return Promise.resolve();
    }

    dispatch({
        type: 'FETCH_WORKFLOWS_REQUEST',
    });

    return api.fetchWorkflows().then(
        response => {

            dispatch({
                type: 'FETCH_WORKFLOWS_SUCCESS',
                response: normalize(response, schema.arrayOfWorkflows),
            });
        },
        error => {

            dispatch({
                type: 'FETCH_WORKFLOWS_FAILURE',
                message: error.message || l10n['something_went_wrong'],
            });
        },
    );
}

/**
 * Action creator for adding a new workflow.
 *
 * @since {{VERSION}}
 *
 * @param {{}} fields Fields to populate the new workflow with.
 */
export const addWorkflow = (fields) => (dispatch, getState) => {

    if ( workflows.getIsAdding(getState().workflows) ) {

        return Promise.resolve();
    }

    dispatch({
        type: 'ADD_WORKFLOW_REQUEST',
    });

    return api.addWorkflow(fields).then(
        response => {

            dispatch({
                type: 'ADD_WORKFLOW_SUCCESS',
                response: normalize(response, schema.workflow),
            });
        },
        error => {

            dispatch({
                type: 'ADD_WORKFLOWS_FAILURE',
                message: error.message || l10n['something_went_wrong'],
            });
        },
    );
};

/**
 * Action creator for editing a workflow.
 *
 * @since {{VERSION}}
 *
 * @param {int} id ID of the workflow to remove.
 */
export const editWorkflow = (id, fields) => (dispatch, getState) => {

    if ( workflows.getIsEditing(getState().workflows) ) {

        return Promise.resolve();
    }

    dispatch({
        type: 'EDIT_WORKFLOW_REQUEST',
        id,
    });

    return api.editWorkflow(id, fields).then(
        response => {
            dispatch({
                type: 'EDIT_WORKFLOW_SUCCESS',
                response: normalize(response, schema.workflow),
            });
        },
        error => {

            dispatch({
                type: 'EDIT_WORKFLOWS_FAILURE',
                message: error.message || l10n['something_went_wrong'],
            });
        },
    );
};

// TODO Loading animation and error handling for delete workflow

/**
 * Action creator for removing a workflow.
 *
 * @since {{VERSION}}
 *
 * @param {int} id ID of the workflow to edit.
 * @param {{}} fields Fields to update the workflow with.
 */
export const deleteWorkflow = (id) => (dispatch, getState) => {

    if ( workflows.getIsDeleting(getState().workflows) ) {

        return Promise.resolve();
    }

    dispatch({
        type: 'DELETE_WORKFLOW_REQUEST',
        id,
    });

    return api.deleteWorkflow(id).then(
        response => {
            dispatch({
                type: 'DELETE_WORKFLOW_SUCCESS',
                response,
            });
        },
        error => {

            dispatch({
                type: 'DELETE_WORKFLOWS_FAILURE',
                message: error.message || l10n['something_went_wrong'],
            });
        },
    );
};