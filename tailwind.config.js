module.exports = {
    purge: [],
    theme: {
        screens: {
            xs: "320px",
            smaller: "541px",
            sm: "640px",
            md: "768px",
            lg: "1024px",
            xl: "1280px"
        },
        extend: {
            flexGrow: {
                "2": 2
            },
            colors: {
                "blue-dark": "#162A44",
                "blue-light": "#DDE6F1",
                "blue-mid": "#426693",
                "blue-mid-light": "#CFDEEB",
                "blue-mid-dark": "#4F6C98",
                "gray-light": "#979DA5",
                "gray-lighter": "#D7E2EF",
                "gray-lightest": "#949DA7",
                "blue-form-input": "#E7F1FE",
                "blue-form-side": "#EDF4F9",
                "blue-form-bottom": "#E1EAF3",
                "blue-form-hover-button": "#334866",
                "blue-form-button": "#4F6C98",
                "blue-border": "#E1EAF3",
                "blue-form-link": "#859DC1",
                "gray-form-label": "#9FA8B2",
                "black-form-text": "#2C2C2C",
                "red-alert": "#FEE9E8",
                "red-alert-text": "#C84347",
                "semi-white": "#FCFCFC"
            },
            padding: {
                input: "0.375rem"
            },
            fontFamily: {
                insomnia: "Verdana, Arial, sans-serif"
            },
            borderWidth: {
                "6": "0.375rem"
            },
            height: {
                "17": "4.25rem",
                "9": "2.25rem"
            },
            width: {
                "9": "2.25rem"
            },
            fontSize: {
                smaller: "0.8125rem"
            }
        }
    },
    variants: {},
    plugins: []
};
