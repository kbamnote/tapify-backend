const fs = require('fs');
const path = require('path');

const dir = path.join(__dirname, 'templates');
const files = fs.readdirSync(dir).filter(f => f.startsWith('vcard') && f.endsWith('.php'));

let count = 0;
files.forEach(file => {
    const filePath = path.join(dir, file);
    let content = fs.readFileSync(filePath, 'utf8');
    
    if (content.includes('A unit of <strong>Mr Print World</strong>')) {
        content = content.replace(
            /A unit of <strong>Mr Print World<\/strong>/g,
            'An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong>'
        );
        fs.writeFileSync(filePath, content, 'utf8');
        count++;
    }
});

console.log(`Successfully updated the footer text in ${count} template files.`);
