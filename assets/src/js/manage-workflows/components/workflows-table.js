import React from 'react';
import {connect} from 'react-redux';

import {WorkflowsTableView} from './workflows-table-view';
import FetchError from './fetch-error';
import {getWorkflows, getIsFetching, getErrorMessage} from '../reducers/workflows';
import * as workflowActions from '../actions/workflows';
import * as modalActions from '../actions/workflow-modal';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Container for showing the table containing all Workflows.
 *
 * @since {{VERSION}}
 *
 * @constructor
 */
class WorkflowsTable extends React.Component {

    /**
     * Fires when component mounts.
     *
     * @since {{VERSION}}
     */
    componentDidMount() {

        this.fetchData();
    }

    /**
     * Fetches the data for the table.
     *
     * @since {{VERSION}}
     */
    fetchData() {

        const {fetchWorkflows} = this.props;

        fetchWorkflows();
    }

    /**
     * Confirms deleting the workflow.
     *
     * @since {{VERSION}}
     *
     * @param {int} id ID of the workflow to delete.
     */
    deleteWorkflowConfirm(id) {

        const {deleteWorkflow} = this.props;

        if (confirm(l10n['confirm_delete_workflow'])) {

            deleteWorkflow(id);
        }
    }

    /**
     * Renders the component.
     *
     * @since {{VERSION}}
     *
     * @returns {XML}
     */
    render() {

        const {store} = this.context;
        const {openWorkflowModal, deleteWorkflow, errorMessage, workflows, isFetching} = this.props;

        if ( isFetching && !workflows.length ) {

            // TODO Improve loading message (put in table with spinner)
            return (
                <p className="wfm-workflows-table-loading">
                    Loading...
                    <span className="spinner is-active inline"/>
                </p>
            );
        }

        if ( errorMessage && !workflows.length ) {

            return (
                <FetchError
                    message={errorMessage}
                    onRetry={() => this.fetchData()}
                />
            );
        }

        return (
            <WorkflowsTableView
                workflows={workflows}
                onWorkflowClick={(id, fields) => openWorkflowModal(id, fields)}
                onDeleteClick={(id) => this.deleteWorkflowConfirm(id)}
                getState={store.getState}
            />
        );
    }
}

WorkflowsTable.contextTypes = {
    store: React.PropTypes.object,
};

/**
 * Maps the new states to receivable props for the WorkflowsTable component.
 *
 * @since {{VERSION}}
 *
 * @param state
 */
const mapStateToProps = (state) => ({
    workflows: getWorkflows(state.workflows),
    isFetching: getIsFetching(state.workflows),
    errorMessage: getErrorMessage(state.workflows),
});

WorkflowsTable = connect(
    mapStateToProps,
    {
        ...workflowActions,
        ...modalActions,
    }
)(WorkflowsTable);

export default WorkflowsTable;