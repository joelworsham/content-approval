import React from 'react';

import Header from './header';
import WorkflowModal from './workflow-modal';
import WorkflowsTable from './workflows-table';

/**
 * The main workflows app class. Magic happens here.
 *
 * @since {{VERSION}}
 */
class App extends React.Component {
    render() {

        return (
            <div>
                <Header />
                <WorkflowModal />
                <WorkflowsTable />
            </div>
        )
    }
}

export default App;