import React from 'react';
import ReactDOM from 'react-dom';
import configureStore from './configure-store';
import Root from './components/root';

const store = configureStore();

ReactDOM.render(
    <Root store={store} />,
    document.getElementById('wfm-manage-workflows')
);