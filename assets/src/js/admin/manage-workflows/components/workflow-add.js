import React from 'react';

import AddWorkflowButton from './add-workflow-button';

import {openWorkflowModal} from '../actions/workflow-modal';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Container for the add workflow button.
 *
 * @since {{VERSION}}
 */
class WorkflowAdd extends React.Component {

    /**
     * Renders the component
     *
     * @since {{VERSION}}
     *
     * @returns {XML}
     */
    render() {

        const { store } = this.context;

        return (
            <AddWorkflowButton
                handleClick={() => {
                    store.dispatch(
                        openWorkflowModal()
                    )
                }}
            >
                {l10n['add_workflow']}
            </AddWorkflowButton>
        );
    }
}

WorkflowAdd.contextTypes = {
    store: React.PropTypes.object,
};

export default WorkflowAdd;