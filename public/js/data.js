// C:\laragon\www\Menu Apps\Bitten Coffee\js\data.js

/**
 * Data Menu Bitten Coffee
 * (Vintage Japanese Izakaya Vibe)
 */
const apiData = {
    restaurant: {
        name: "BITTEN COFFEE",
        tagline: "Vintage Izakaya",
        table: "04"
    },
    
    // Data untuk Banner Carousel
    highlights: [
        { id: 'h1', img: 'Assest/Caraousel/Hero 1.jpg', title: 'New From Us', desc: '' },
        { id: 'h2', img: 'Assest/Caraousel/Hero 2.png', title: 'New From Us', desc: '' },
        { id: 'h3', img: 'Assest/Caraousel/Hero 3.jpg', title: 'New From Us', desc: '' }
    ],

    categories: [
        { id: 'all', name: 'All Menu', icon: 'fa-star' },
        { id: 'beverages', name: 'Beverages', icon: 'fa-coffee' },
        { id: 'foods', name: 'Foods', icon: 'fa-utensils' },
        { id: 'snacks', name: 'Snacks', icon: 'fa-cookie' },
        { id: 'sweets', name: 'Sweets', icon: 'fa-ice-cream' }
    ],
    
    menu: [
        // ==========================================
        // 1. BEVERAGES
        // ==========================================
        { id: 'bev-1', categoryId: 'beverages', name: 'Americano', price: 18000, desc: 'Kopi hitam klasik yang pekat dan menyegarkan.', img: 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=300&h=300&fit=crop' },
        { id: 'bev-2', categoryId: 'beverages', name: 'Latte', price: 21000, desc: 'Espresso dengan paduan susu creamy.', img: 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=300&h=300&fit=crop' },
        { id: 'bev-3', categoryId: 'beverages', name: 'Mocha', price: 22000, desc: 'Kombinasi espresso, cokelat, dan susu.', img: 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300&h=300&fit=crop' },
        { id: 'bev-4', categoryId: 'beverages', name: 'Split', price: 25000, desc: 'Sajian unik double shot espresso.', img: 'https://images.unsplash.com/photo-1619614488318-6bb731a547fa?w=300&h=300&fit=crop' },
        { id: 'bev-5', categoryId: 'beverages', name: 'Buraun Shuga', price: 18000, desc: 'Kopi susu gula aren khas Bitten Coffee.', img: 'https://images.unsplash.com/photo-1611162458324-aae1eb4129a4?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-6', categoryId: 'beverages', name: 'Vanilla Latte', price: 20000, desc: 'Latte dengan sirup vanilla yang harum.', img: 'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=300&h=300&fit=crop' },
        { id: 'bev-7', categoryId: 'beverages', name: 'Caramel Latte', price: 20000, desc: 'Latte dengan sentuhan karamel manis.', img: 'https://images.unsplash.com/photo-1589396575653-c09c794f6d74?w=300&h=300&fit=crop' },
        { id: 'bev-8', categoryId: 'beverages', name: 'Hazelnut Latte', price: 20000, desc: 'Latte beraroma kacang hazelnut panggang.', img: 'https://images.unsplash.com/photo-1550478051-fb8f00db10a8?w=300&h=300&fit=crop' },
        { id: 'bev-9', categoryId: 'beverages', name: 'Irish Latte', price: 22000, desc: 'Sensasi klasik sirup Irish (non-alcohol) dalam balutan latte.', img: 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-10', categoryId: 'beverages', name: 'Baileys', price: 25000, desc: 'Kopi susu dengan rasa Baileys khas (non-alcohol).', img: 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-11', categoryId: 'beverages', name: 'Gurin (Matcha)', price: 20000, desc: 'Matcha latte Jepang yang otentik.', img: 'https://images.unsplash.com/photo-1515823662972-da6a29051671?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-12', categoryId: 'beverages', name: 'Usucha', price: 38000, desc: 'Teh hijau matcha tradisional yang ringan dan berbuih.', img: 'https://images.unsplash.com/photo-1536935338788-846bb9981813?w=300&h=300&fit=crop' },
        { id: 'bev-13', categoryId: 'beverages', name: 'Pure Matcha Latte', price: 38000, desc: 'Paduan pure matcha premium dengan susu.', img: 'https://images.unsplash.com/photo-1572490122747-3968b75bb8ef?w=300&h=300&fit=crop' },
        { id: 'bev-14', categoryId: 'beverages', name: 'Strawberry Matcha', price: 45000, desc: 'Kombinasi unik selai stroberi segar dan matcha murni.', img: 'https://images.unsplash.com/photo-1620755913498-75f85e17f766?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-15', categoryId: 'beverages', name: 'Chocolate', price: 20000, desc: 'Cokelat klasik yang creamy dan memanjakan.', img: 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-16', categoryId: 'beverages', name: 'Reddo', price: 20000, desc: 'Red velvet latte yang creamy dan memikat.', img: 'https://images.unsplash.com/photo-1620755913498-75f85e17f766?w=300&h=300&fit=crop' },
        { id: 'bev-17', categoryId: 'beverages', name: 'Ube', price: 18000, desc: 'Minuman ube ungu yang manis dan unik.', img: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&h=300&fit=crop' },
        { id: 'bev-18', categoryId: 'beverages', name: 'Banami', price: 22000, desc: 'Susu pisang ala Korea yang menyegarkan.', img: 'https://images.unsplash.com/photo-1595085610896-bc3ce6fffc05?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-19', categoryId: 'beverages', name: 'Korean Strawberry Milk', price: 27000, desc: 'Susu dengan potongan stroberi segar ala Korea.', img: 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-20', categoryId: 'beverages', name: 'Biscoff Cookies n Cream', price: 27000, desc: 'Blended minuman dengan biskuit Biscoff yang lezat.', img: 'https://images.unsplash.com/photo-1572490122747-3968b75bb8ef?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-21', categoryId: 'beverages', name: 'Kyoto Strawberry Tea', price: 21000, desc: 'Teh aromatik dipadukan dengan stroberi ala Kyoto.', img: 'https://images.unsplash.com/photo-1499638673689-79a0b5115d87?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-22', categoryId: 'beverages', name: 'Bitten Lemonade', price: 21000, desc: 'Lemonade khas Bitten yang super segar.', img: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'bev-23', categoryId: 'beverages', name: 'Sodamericano', price: 25000, desc: 'Americano dicampur dengan soda yang mengejutkan. Pilihan rasa: Lychee/Peach/Tamarind.', img: 'https://images.unsplash.com/photo-1556767716-1b1e95cfc37e?w=300&h=300&fit=crop', tags: ['best'] },
        
        // ==========================================
        // 2. FOODS
        // ==========================================
        { id: 'food-1', categoryId: 'foods', name: 'Rice Bowl Chicken Teriyaki', price: 25000, desc: 'Ayam teriyaki autentik dengan nasi hangat.', img: 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-2', categoryId: 'foods', name: 'Rice Bowl Chicken Blackpepper', price: 25000, desc: 'Ayam saus lada hitam khas yang pedas menghangatkan.', img: 'https://images.unsplash.com/photo-1544927233-a3d8b139fcb0?w=300&h=300&fit=crop', tags: ['best', 'spicy'] },
        { id: 'food-3', categoryId: 'foods', name: 'Rice Bowl Chicken Cabe Garam', price: 25000, desc: 'Ayam krispi bumbu cabe garam.', img: 'https://images.unsplash.com/photo-1623341214825-9f4f963727da?w=300&h=300&fit=crop', tags: ['spicy'] },
        { id: 'food-4', categoryId: 'foods', name: 'Rice Bowl Dori Sambal Matah', price: 30000, desc: 'Ikan dori lembut dengan sambal matah pedas segar.', img: 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=300&h=300&fit=crop', tags: ['best', 'spicy'] },
        { id: 'food-5', categoryId: 'foods', name: 'Rice Bowl Ebi Furai Mayo', price: 30000, desc: 'Udang goreng balut tepung panko dengan saus mayo.', img: 'https://images.unsplash.com/photo-1626200419188-3485ab9d71c1?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-6', categoryId: 'foods', name: 'Thai Beef Basil', price: 35000, desc: 'Tumisan sapi khas Thai dengan daun kemangi yang wangi.', img: 'https://images.unsplash.com/photo-1544927233-a3d8b139fcb0?w=300&h=300&fit=crop', tags: ['best', 'spicy'] },
        { id: 'food-7', categoryId: 'foods', name: 'Smoky Beef BBQ', price: 35000, desc: 'Daging sapi panggang dengan saus BBQ berasap.', img: 'https://images.unsplash.com/photo-1548943487-a2e4e43b4859?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-8', categoryId: 'foods', name: 'Nasi Goreng Katsu', price: 32000, desc: 'Nasi goreng tradisional yang disajikan dengan chicken katsu.', img: 'https://images.unsplash.com/photo-1604908177525-4c07d3b84cb5?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-9', categoryId: 'foods', name: 'Ramen Katsu', price: 35000, desc: 'Ramen khas Izakaya dengan topping chicken katsu.', img: 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-10', categoryId: 'foods', name: 'Ramen Bitten Ebi', price: 38000, desc: 'Ramen dengan topping udang ebi furai krispi.', img: 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'food-11', categoryId: 'foods', name: 'Ramen TanTan', price: 28000, desc: 'Ramen kuah wijen pedas berempah gurih.', img: 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=300&h=300&fit=crop', tags: ['spicy'] },

        // ==========================================
        // 3. SNACK SAVORIES
        // ==========================================
        { id: 'snk-1', categoryId: 'snacks', name: 'Jamur Krispy', price: 22000, desc: 'Jamur enoki / tiram goreng tepung renyah.', img: 'https://images.unsplash.com/photo-1544025162-81111420d6aa?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'snk-2', categoryId: 'snacks', name: 'French Fries', price: 18000, desc: 'Kentang goreng klasik yang disukai semua orang.', img: 'https://images.unsplash.com/photo-1576107232684-1279f390859f?w=300&h=300&fit=crop' },
        { id: 'snk-3', categoryId: 'snacks', name: 'Tahu Krispy', price: 20000, desc: 'Tahu goreng garing.', img: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=300&h=300&fit=crop' },
        { id: 'snk-4', categoryId: 'snacks', name: 'Gyoza', price: 20000, desc: 'Pangsit Jepang isi ayam yang dipanggang bagian bawahnya.', img: 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=300&h=300&fit=crop' },

        // ==========================================
        // 4. SWEETS & SHARING
        // ==========================================
        { id: 'swt-1', categoryId: 'sweets', name: 'Bitten Sampler (u/ 2 orang)', price: 35000, desc: 'Piring berbagi yang berisi bermacam gorengan. Pilihan bumbu: Balado / Jagung Bakar / Blackpepper / Garlic.', img: 'https://images.unsplash.com/photo-1582283995894-386d499ecf20?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'swt-2', categoryId: 'sweets', name: 'Bitten Platter (u/ 4 orang)', price: 55000, desc: 'Porsi besar cocok untuk kumpul-kumpul bareng teman.', img: 'https://images.unsplash.com/photo-1585325701165-351af916e581?w=300&h=300&fit=crop' },
        { id: 'swt-3', categoryId: 'sweets', name: 'Pisang Lumer', price: 25000, desc: 'Pisang goreng berbalut lapisan manis lumer.', img: 'https://images.unsplash.com/photo-1601000938259-9e92002320b2?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'swt-4', categoryId: 'sweets', name: 'Bitten Waffle', price: 30000, desc: 'Wafel khas dengan topping es krim dan sirup.', img: 'https://images.unsplash.com/photo-1484723091791-c0e7e14fac16?w=300&h=300&fit=crop', tags: ['best'] },
        { id: 'swt-5', categoryId: 'sweets', name: 'Classic Croffle (3 pcs)', price: 30000, desc: 'Croissant waffle renyah berlapis gula karamel.', img: 'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?w=300&h=300&fit=crop' }
    ]
};
