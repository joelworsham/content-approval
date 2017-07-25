import React from 'react';

import WorkflowForm from './workflow-form';
import Modal from './modal';

import {closeWorkflowModal} from '../actions/workflow-modal';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Container for the Workflow Modal.
 *
 * @since {{VERSION}}
 */
class WorkflowModal extends React.Component {

    componentDidMount() {

        const {store} = this.context;

        this.unsubscribe = store.subscribe(() => {
            this.forceUpdate();
        });
    }

    componentWillUnmount() {

        this.unsubscribe();
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
        const state   = store.getState();
        const component = this;

        // Only show if open
        if ( !state.workflowModal.open ) {

            return null;
        }

        return (
                <Modal
                    title={l10n['add_workflow']}
                    submitText={l10n['save_workflow']}

                    handleClose={() => {
                        store.dispatch(closeWorkflowModal())
                    }}
                    handleSubmit={() => {
                        component.refs.workflowForm.submit();
                    }}
                >
                    <WorkflowForm ref="workflowForm" />
                </Modal>
        )
    }
}

WorkflowModal.contextTypes = {
    store: React.PropTypes.object,
};

export default WorkflowModal;