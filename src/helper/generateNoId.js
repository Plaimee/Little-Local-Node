const generateNoId = () => {
    const now = new Date();
    const ymd = now.getFullYear().toString() + 
                         (now.getMonth() + 1).toString().padStart(2, '0') + 
                         now.getDate().toString().padStart(2, '0');
    const randLetters = Array(4).fill(null).map(() => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join("");
    const randNumbers = Array(3).fill(null).map(() => Math.floor(Math.random() * 10)).join("");

    return `${ymd}${randLetters}${randNumbers}`;
}

module.exports = { generateNoId };