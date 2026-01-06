const thousandsOptions = {
    mask: Number, // enable number mask
    signed: false,
    thousandsSeparator: ".",
    scale: 0, // digits after point, 0 for integers
    min: 0,
    max: 100000000000000,
    mapToRadix: [","],
    radix: ",",
};
const decimalsOptions = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    // max: 10,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 2,
    padFractionalZeros: true,
};

const thousandsOptionsCarton = {
    mask: Number, // enable number mask
    signed: false,
    thousandsSeparator: ".",
    max: 10000,
};

const decimalsOptionsCarton = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    max: 10,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 2,
    padFractionalZeros: true,
};

const fourDecimalsOptions = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    // max: 10,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 3,
    padFractionalZeros: true,
};

const oneDecimal = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    max: 100000,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 1,
    padFractionalZeros: true,
};

const twoDecimal = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    max: 100000,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 2,
    padFractionalZeros: true,
};

const cuatroDecimalsOptions = {
    mask: Number,
    signed: false,
    thousandsSeparator: ".",
    // max: 10,
    mapToRadix: [","],
    radix: ",",
    normalizeZeros: true,
    scale: 4,
    padFractionalZeros: true,
};
