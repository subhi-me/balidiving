const express = require('express');
const path = require('path');
const app = express();

// Setup EJS
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Static files
app.use(express.static(path.join(__dirname, 'public')));

// Routes
app.get('/', (req, res) => {
    res.render('pages/home', {
        title: 'Bali Diving - Explore Underwater Paradise',
        packages: [
            { name: 'Beginner Dive', price: '$99' },
            { name: 'Advanced Dive', price: '$149' },
            { name: 'Night Dive', price: '$199' }
        ],
        testimonials: [
            { name: 'Sarah J.', quote: 'Best diving experience ever!' },
            { name: 'Mike T.', quote: 'The instructors were amazing.' }
        ]
    });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});