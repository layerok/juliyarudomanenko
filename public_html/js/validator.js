(function () {
    const createRule = (key, testFn) => {
        return {
            key,
            testFn
        }
    }

    const ruleDefinitions = [
        createRule('required', (value) => value !== ""),
        createRule('phoneUa', (value) => /^\+?3?8?\(?0\d{2}\)?\-?\d{3}\-?\d{2}\d{2}$/.test(value))
    ]

    function useValidator(options) {
        const {rules: providedRules} = options;

        const listeners = [];

        const addListener = (fn) => {
            listeners.push(fn)
            return () => {
                const index = listeners.indexOf(fn);
                if (index !== -1) {
                    listeners.splice(listeners.indexOf(fn), 1);
                }
            }
        }

        const validate = (data) => {
            let isValid = true;

            const elementNames = Object.keys(providedRules);

            for(let i = 0; i < elementNames.length; i++) {
                const itemKey = elementNames[i];

                const value = data[itemKey];

                if (!validateSingle(itemKey, value)) {
                    isValid = false;
                }
            }


            return isValid;
        }

        const validateSingle = (key, value) => {
            const rules = providedRules[key];

            for (let i = 0; i < rules.length; i++) {
                const ruleKey = rules[i];

                const rule = ruleDefinitions.find(rule => rule.key === ruleKey);

                if (!rule) {
                    throw Error(`validation rule ${ruleKey} is not found`)
                }

                const result = rule.testFn(value);

                listeners.map(listener => listener(result, key, ruleKey));

                if (!result) {
                    return false;
                }
            }


            return true;

        }


        return {
            validate,
            validateSingle,
            addListener,
        }

    }

    window.useValidator = useValidator;
})()