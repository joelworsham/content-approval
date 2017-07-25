import {combineReducers} from 'redux';
import workflows from './workflows';
import workflowModal from './workflow-modal';

/**
 * Workflow primary reducer application.
 *
 * @since {{VERSION}}
 *
 * @type {Reducer<S>}
 */
const workflowApp = combineReducers({
    workflows,
    workflowModal,
});

export default workflowApp;