import React from 'react';
import Select from 'react-select';

const l10n = WFM_ManageWorkflows['l10n'];

class FieldText extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            value: this.props.value || '',
        };
    }

    render() {

        const component = this;

        return (
            <div className={(this.props.classes || '') + ' wfm-field wfm-field-text' +
            (this.props.error ? ' wfm-field-error' : '')}>
                {this.props.label && <label>{this.props.label}</label>}

                <input
                    type="text"
                    name={this.props.name}
                    disabled={this.props.disabled || false}
                    placeholder={this.props.placeholder}
                    value={this.state.value}
                    onChange={(e) => {
                        component.setState({value: e.target.value});
                        component.props.onChange(e.target.value);
                    }}
                />

                {this.props.description &&
                <div className="wfm-field-description">
                    {this.props.description}
                </div>
                }

                {this.props.error &&
                <div className="wfm-field-error-message">
                    {this.props.error}
                </div>
                }
            </div>
        )
    }
}

/**
 * Outputs the select field.
 *
 * @since {{VERSION}}
 */
class FieldSelect extends React.Component {

    constructor(props) {

        super(props);

        this.state = {
            value: this.props.value,
        };

        this.onChange = this.onChange.bind(this);
    }

    /**
     * Fires on change.
     *
     * The main purpose is to consolidate the value from label and value to only value.
     *
     * @param value
     */
    onChange(value) {

        this.setState({value: value});

        let newValues = [];

        for (let i = 0; i < value.length; i++) {

            newValues.push(value[i].value);
        }

        this.props.onChange(newValues);
    }

    render() {

        const translations = {
            noResultsText: l10n['noResultsText'],
            searchPromptText: l10n['searchPromptText'],
            loadingPlaceholder: l10n['loadingPlaceholder'],
            clearValueText: l10n['clearValueText'],
            clearAllText: l10n['clearAllText'],
            backspaceToRemoveMessage: l10n['backspaceToRemoveMessage'],
            addLabelText: l10n['addLabelText'],
        };

        let selectComponent;

        const component = this;

        if ( this.props.loadOptions ) {

            selectComponent =
                <Select.Async
                    name={this.props.name}
                    value={this.state.value}
                    disabled={this.props.disabled || false}
                    placehodler={this.props.placeholder}
                    multi={this.props.multiple}
                    loadOptions={this.props.loadOptions}
                    onChange={this.onChange}
                    {...translations}

                />

        } else {

            selectComponent = <Select
                name={this.props.name}
                value={this.state.value}
                disabled={this.props.disabled || false}
                placehodler={this.props.placeholder}
                multi={this.props.multiple}
                options={this.props.options}
                onChange={this.onChange}
                {...translations}
            />
        }

        return (
            <div className={(this.props.classes || '') + ' wfm-field wfm-field-select' +
            (this.props.error ? ' wfm-field-error' : '')}>
                {this.props.label && <label>{this.props.label}</label>}

                {selectComponent}

                {this.props.description &&
                <div className="wfm-field-description">
                    {this.props.description}
                </div>
                }

                {this.props.error &&
                <div className="wfm-field-error-message">
                    {this.props.error}
                </div>
                }
            </div>
        );
    }
}
;

/**
 * Outputs the hidden field.
 *
 * @since {{VERSION}}
 *
 * @param name
 * @param required
 * @constructor
 */
const FieldHidden = ({
                         name,
                         value,
                         disabled,
                         classes,
                     }) => {

    const inputArgs = {
        type: 'hidden',
        disabled: disabled,
        name: name,
        value: value,
    };

    return (
        <div className={(classes || '') + ' wfm-field wfm-field-hidden'}>
            <input
                {...inputArgs}
            />
        </div>
    );
};

/**
 * Outputs the HTML field.
 *
 * @since {{VERSION}}
 *
 * @param name
 * @param required
 * @constructor
 */
const FieldHTML = ({
                       label,
                       description,
                       classes,
                   }) => {

    return (
        <div className={(classes || '') + ' wfm-field wfm-field-html'}>
            {label && <label>{label}</label>}

            {description &&
            <div className="wfm-field-description">
                {description}
            </div>
            }
        </div>
    );
};

export {
    FieldText,
    FieldSelect,
    FieldHidden,
    FieldHTML,
}