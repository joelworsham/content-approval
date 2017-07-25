import React, {PropTypes} from 'react';
import {Provider} from 'react-redux';
import App from './';

/**
 * Root component for the app.
 *
 * @since {{VERSION}}
 *
 * @param store
 * @constructor
 */
const Root = ({store}) => (
    <Provider store={store}>
        <App />
    </Provider>
);

Root.propTypes = {
    store: PropTypes.object.isRequired,
};

export default Root;