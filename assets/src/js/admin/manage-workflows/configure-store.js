import {createStore, applyMiddleware} from 'redux';
import createLogger from 'redux-logger';
import thunk from 'redux-thunk';
import workflowApp from './reducers';

/**
 * Configures the store for the app.
 *
 * @since {{VERSION}}
 *
 * @returns {Store<S>}
 */
const configureStore = () => {

    const middlewares = [thunk];

    if ( process.env.NODE_ENV !== 'production' ) {

        middlewares.push(createLogger);
    }

    return createStore(
        workflowApp,
        applyMiddleware(...middlewares),
    );
}

export default configureStore;