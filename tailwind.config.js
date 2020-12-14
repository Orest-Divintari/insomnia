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
            minHeight: {
                "64": "16rem",
                "32": "8rem"
            },
            maxHeight: {
                "64": "16rem",
                "96": "24rem"
            },
            flexGrow: {
                "2": 2
            },
            colors: {
                gallery: "#EEEEEE",
                "green-mid": "#048004",
                "blue-dark": "#162A44",
                "blue-lighter": "#EDF4F9",
                "blue-light": "#DDE6F1",
                "blue-mid": "#426693",
                "blue-mid-light": "#CFDEEB",
                "blue-mid-dark": "#4F6C98",
                "blue-reply-border": "#A2BBDF",
                "blue-link-menu": "#2C435F",
                "gray-50": "#F0F0F0",
                "gray-light": "#979DA5",
                "gray-lighter": "#D7E2EF",
                "gray-lightest": "#949DA7",
                "gray-mid": "#6C7680",
                "gray-geyser": "#D3DBE3",
                "gray-loblolly": "#C5CCD2",
                "blue-form-input": "#E7F1FE",
                "blue-like": "#0070D6",
                "blue-ship-cove": "#6686B1",
                "blue-link": "#4d6c99",
                "blue-link-hover": "#6686B1",
                "blue-botticelli": "#D3DDE9",

                "white-catskill": "#E1EAF3",
                "blue-form-hover-button": "#334866",
                "blue-form-link": "#859DC1",
                "gray-form-label": "#9FA8B2",
                "black-form-text": "#2C2C2C",
                "red-alert": "#FEE9E8",
                "red-alert-text": "#C84347",
                'red-dark': "#CA0003",
                "semi-white": "#FCFCFC",
                "semi-white-mid": "#FAFAFA",
                "purple-muted": "#75587A",
                "black-semi": "#2C2C2C"
            },
            spacing: {
                "1/2": "0.125rem",
                tiny: "0.15rem",
                "3/2": "0.35rem",
                "5/2": "0.625rem",
                "7": "1.75rem",
                "9": "2.25rem",
                "14": "3.5rem",
                "17": "4.25rem",
                "28": "7rem",
                "72": "18rem",
                "80": "20rem"
            },
            padding: {
                input: "0.375rem"
            },
            fontFamily: {
                insomnia: "Verdana, Arial, sans-serif"
            },
            borderWidth: {
                "6": "0.375rem",
                "3": "3px"
            },

            fontSize: {
                "4xs": "0.1rem",
                "3xs": "0.25rem",
                "2xs": "0.6rem",
                smaller: "0.8125rem",
                md: "0.9375rem"
            }
        }
    },
    variants: {
            
            textColor: ["responsive", "hover", "focus", "active", "group-hover"],
            textDecoration: ['hover','group-hover'],
            cursor: ['hover', 'group-hover']
    },
    plugins: []
};
