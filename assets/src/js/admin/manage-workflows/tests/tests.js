/**
 * Tests file for the app suite.
 *
 * This file should be appended in the Gulp build script for development, but not in the final dist build script.
 *
 * @since {{VERSION}}
 */

import {
    testAddWorkflow,
    testRemoveWorkflow,
    testEditWorkflow,
} from './workflows.js';
import {
    testOpenWorkflowModal,
    testCloseWorkflowModal,
    testUpdateWorkflowModalField,
} from './workflow-modal';

testAddWorkflow();
testRemoveWorkflow();
testEditWorkflow();
testOpenWorkflowModal();
testCloseWorkflowModal();
testUpdateWorkflowModalField();

console.log('All Manage Workflows APP tests passed!');