exports.home = (req, res) => {
    res.render('pages/home', { title: 'Home Page' });
};

exports.about = (req, res) => {
    res.render('pages/about', { title: 'About Us' });
};

exports.contact = (req, res) => {
    res.render('pages/contact', { title: 'Contact Us' });
};
