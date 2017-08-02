import expect from 'expect';
import deepFreeze from 'deep-freeze';
import workflows from '../reducers/workflows';

const testAddWorkflow = () => {

    const stateBefore = [];
    const action      = {
        type: 'ADD_WORKFLOW',
        id: 0,
        fields: {
            title: 'Workflow A',
        },
    };
    const stateAfter  = [
        {
            id: 0,
            fields: {
                title: 'Workflow A',
            }
        },
    ];

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflows(stateBefore, action)
    ).toEqual(stateAfter);
}

const testRemoveWorkflow = () => {

    const stateBefore = [
        {
            id: 0,
            fields: {
                title: 'Workflow A'
            },
        },
        {
            id: 1,
            fields: {
                title: 'Workflow B'
            },
        },
    ];
    const action      = {
        type: 'REMOVE_WORKFLOW',
        id: 1,
    };
    const stateAfter  = [
        {
            id: 0,
            fields: {
                title: 'Workflow A'
            },
        },
    ];

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflows(stateBefore, action)
    ).toEqual(stateAfter);
}

const testEditWorkflow = () => {

    const stateBefore = [
        {
            id: 0,
            fields: {
                title: 'Workflow A'
            },
        },
        {
            id: 1,
            fields: {
                title: 'Workflow B'
            },
        },
    ];
    const action      = {
        type: 'EDIT_WORKFLOW',
        id: 1,
        fields: {
            title: 'Workflow New',
        },
    };
    const stateAfter  = [
        {
            id: 0,
            fields: {
                title: 'Workflow A'
            },
        },
        {
            id: 1,
            fields: {
                title: 'Workflow New'
            },
        },
    ];

    deepFreeze(stateBefore);
    deepFreeze(action);

    expect(
        workflows(stateBefore, action)
    ).toEqual(stateAfter);
}

export {
    testAddWorkflow,
    testRemoveWorkflow,
    testEditWorkflow,
}