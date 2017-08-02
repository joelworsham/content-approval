import React from 'react';
import {WorkflowRow} from './workflow-row';
import * as workflowsReducer from '../reducers/workflows';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Component for showing the table containing all Workflows.
 *
 * @since {{VERSION}}
 *
 * @param {[]} workflows Array of all workflows to show.
 * @param {func} onWorkflowClick Handler for clicking a workflow.
 * @constructor
 */
export const WorkflowsTableView = ({
    workflows,
    onWorkflowClick,
    onDeleteClick,
    getState,
}) => (
    <table className="wfm-workflows-table">
        <tbody>
        {workflows.length > 0 ?
            workflows.map(workflow => (
                <WorkflowRow
                    key={workflow.id}
                    onTitleClick={() => onWorkflowClick(workflow.id, workflow.fields)}
                    onDeleteClick={() => onDeleteClick(workflow.id)}
                    isDeleting={workflowsReducer.getIsDeleting(getState().workflows) === workflow.id}
                    {...workflow.fields}
                />
            )) :
            <tr>
                <td>
                    {l10n['no_workflows']}
                </td>
            </tr>
        }
        </tbody>
    </table>
);

/*
 () => {

 let fields = {};

 workflows.map(w => {

 if ( w.id === workflow.id ) {

 fields = w.fields;
 }
 });

 store.dispatch(
 openWorkflowModal(workflow.id, fields)
 )
 }
 */