import expect from 'expect';
import deepFreeze from 'deep-freeze';
import workflowModal from '../reducers/workflow-modal';

const testOpenWorkflowModal = () => {

    const stateBefore = {
        open: false,
    };
    const action      = {
        type: 'OPEN_WORKFLOWMODAL',
        id: 0,
        fields: {
            fieldA: 'Field Value',
        },
    };
    const stateAfter  = {
        open: true,
        id: 0,
        fields: {
            fieldA: 'Field Value',
        },
    }

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflowModal(stateBefore, action)
    ).toEqual(stateAfter);
}

const testCloseWorkflowModal = () => {

    const stateBefore = {
        open: true,
    };
    const action      = {
        type: 'CLOSE_WORKFLOWMODAL',
    };
    const stateAfter  = {
        open: false,
    }

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflowModal(stateBefore, action)
    ).toEqual(stateAfter);
}

const testUpdateWorkflowModalField = () => {

    const stateBefore = {
        open: true,
        id: 0,
        fields: {
            test1: 1,
            test2: 2,
        }
    };
    const action      = {
        type: 'UPDATE_WORKFLOWMODAL_FIELD',
        name: 'test2',
        value: 6,
    };
    const stateAfter  = {
        open: true,
        id: 0,
        fields: {
            test1: 1,
            test2: 6,
        }
    }

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflowModal(stateBefore, action)
    ).toEqual(stateAfter);
}

export {
    testOpenWorkflowModal,
    testCloseWorkflowModal,
    testUpdateWorkflowModalField,
}