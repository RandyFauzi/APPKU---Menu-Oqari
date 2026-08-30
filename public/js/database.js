// --- BITTEN COFFEE LOCAL DB ENGINE ---
// Menangani semua operasi database (statis berbasis LocalStorage)

const DB = {
    // 1. Fungsi READ (Ambil Data)
    get: function(tableName) {
        let data = localStorage.getItem(tableName);
        return data ? JSON.parse(data) : [];
    },

    // 2. Fungsi SAVE (Simpan/Update Data)
    save: function(tableName, dataArray) {
        localStorage.setItem(tableName, JSON.stringify(dataArray));
        // Memicu event storage secara manual untuk sinkronisasi tab
        window.dispatchEvent(new Event('storage'));
    },

    // 3. Fungsi SEEDING MENU (Isi data awal jika kosong)
    initMenu: function() {
        if (this.get('bitten_menu').length < 70) {
            const initialMenu = [
    {
        "name": "Americano",
        "price": 18000,
        "desc": "Kopi hitam klasik yang pekat dan menyegarkan.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1551030173-122aabc4489c?w=500&h=500&fit=crop",
        "id": "m1",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Latte",
        "price": 21000,
        "desc": "Espresso dengan paduan susu creamy.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&h=500&fit=crop",
        "id": "m2",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Mocha",
        "price": 22000,
        "desc": "Kombinasi espresso, cokelat, dan susu.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&h=500&fit=crop",
        "id": "m3",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Split",
        "price": 25000,
        "desc": "Sajian unik double shot espresso.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1619614488318-6bb731a547fa?w=500&h=500&fit=crop",
        "id": "m4",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Buraun Shuga",
        "price": 18000,
        "desc": "Kopi susu gula aren khas Bitten Coffee.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1611162458324-aae1eb4129a4?w=500&h=500&fit=crop",
        "id": "m5",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Vanilla Latte",
        "price": 20000,
        "desc": "Latte dengan sirup vanilla yang harum.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=500&h=500&fit=crop",
        "id": "m6",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Caramel Latte",
        "price": 20000,
        "desc": "Latte dengan sentuhan karamel manis.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1589396575653-c09c794f6d74?w=500&h=500&fit=crop",
        "id": "m7",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Hazelnut Latte",
        "price": 20000,
        "desc": "Latte beraroma kacang hazelnut panggang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1550478051-fb8f00db10a8?w=500&h=500&fit=crop",
        "id": "m8",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Irish Latte",
        "price": 22000,
        "desc": "Sensasi klasik sirup Irish (non-alcohol).",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=500&h=500&fit=crop",
        "id": "m9",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Baileys",
        "price": 25000,
        "desc": "Kopi susu dengan rasa Baileys khas (non-alcohol).",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=500&h=500&fit=crop",
        "id": "m10",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Gurin",
        "price": 20000,
        "desc": "Matcha latte Jepang yang otentik.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1515823662972-da6a29051671?w=500&h=500&fit=crop",
        "id": "m11",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Usucha",
        "price": 38000,
        "desc": "Teh hijau matcha tradisional.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1536935338788-846bb9981813?w=500&h=500&fit=crop",
        "id": "m12",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Pure Matcha Latte",
        "price": 38000,
        "desc": "Paduan pure matcha premium dengan susu.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1572490122747-3968b75bb8ef?w=500&h=500&fit=crop",
        "id": "m13",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Strawberry Matcha",
        "price": 45000,
        "desc": "Kombinasi unik selai stroberi segar dan matcha murni.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1620755913498-75f85e17f766?w=500&h=500&fit=crop",
        "id": "m14",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Chocolate",
        "price": 20000,
        "desc": "Cokelat klasik yang creamy dan memanjakan.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&h=500&fit=crop",
        "id": "m15",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Reddo",
        "price": 20000,
        "desc": "Red velvet latte yang creamy.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1620755913498-75f85e17f766?w=500&h=500&fit=crop",
        "id": "m16",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Ube",
        "price": 18000,
        "desc": "Minuman ube ungu yang manis.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&h=500&fit=crop",
        "id": "m17",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Buru",
        "price": 20000,
        "desc": "Minuman susu segar.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&h=500&fit=crop",
        "id": "m18",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Banami",
        "price": 22000,
        "desc": "Susu pisang ala Korea.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1595085610896-bc3ce6fffc05?w=500&h=500&fit=crop",
        "id": "m19",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Cotton Candy",
        "price": 22000,
        "desc": "Susu manis rasa permen kapas.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1579954115545-a95591f28bfc?w=500&h=500&fit=crop",
        "id": "m20",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Hazelnut Choco",
        "price": 24000,
        "desc": "Paduan cokelat dan hazelnut.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&h=500&fit=crop",
        "id": "m21",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Korean Strawberry Milk",
        "price": 27000,
        "desc": "Susu dengan potongan stroberi segar ala Korea.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=500&h=500&fit=crop",
        "id": "m22",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Vanilla Frappe",
        "price": 24000,
        "desc": "Frappe vanilla yang menyegarkan.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1572490122747-3968b75bb8ef?w=500&h=500&fit=crop",
        "id": "m23",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Choco Frappe",
        "price": 24000,
        "desc": "Frappe cokelat yang kaya rasa.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1553177595-4de2bb0842b9?w=500&h=500&fit=crop",
        "id": "m24",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Matcha Frappe",
        "price": 24000,
        "desc": "Matcha blended dingin.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1536935338788-846bb9981813?w=500&h=500&fit=crop",
        "id": "m25",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Biscoff Cookies n Cream",
        "price": 27000,
        "desc": "Blended minuman dengan biskuit Biscoff.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1572490122747-3968b75bb8ef?w=500&h=500&fit=crop",
        "id": "m26",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Unicorn Frappe",
        "price": 27000,
        "desc": "Frappe warna-warni yang manis.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1553177595-4de2bb0842b9?w=500&h=500&fit=crop",
        "id": "m27",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Caramel Coffee Frappe",
        "price": 30000,
        "desc": "Frappe kopi dengan saus karamel.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1589396575653-c09c794f6d74?w=500&h=500&fit=crop",
        "id": "m28",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Baileys Coffee Frappe",
        "price": 32000,
        "desc": "Frappe kopi dengan sirup Baileys (non-alcohol).",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=500&h=500&fit=crop",
        "id": "m29",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Ocha (free refill)",
        "price": 18000,
        "desc": "Teh hijau Jepang asli.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&h=500&fit=crop",
        "id": "m30",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Lychee Tea",
        "price": 25000,
        "desc": "Teh dengan rasa buah leci manis.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&h=500&fit=crop",
        "id": "m31",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Lemon Tea",
        "price": 21000,
        "desc": "Teh lemon yang menyegarkan.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&h=500&fit=crop",
        "id": "m32",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Kyoto Strawberry Tea",
        "price": 21000,
        "desc": "Teh aromatik dipadukan dengan stroberi ala Kyoto.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1499638673689-79a0b5115d87?w=500&h=500&fit=crop",
        "id": "m33",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Bitten Strawberry Yakult",
        "price": 27000,
        "desc": "Minuman probiotik dengan stroberi.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&h=500&fit=crop",
        "id": "m34",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Bitten Lemoneade",
        "price": 21000,
        "desc": "Lemonade khas Bitten yang super segar.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&h=500&fit=crop",
        "id": "m35",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Osaka Lemoneade",
        "price": 24000,
        "desc": "Lemonade ala Jepang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&h=500&fit=crop",
        "id": "m36",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Sodamericano",
        "price": 25000,
        "desc": "Americano dicampur dengan soda yang mengejutkan.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1556767716-1b1e95cfc37e?w=500&h=500&fit=crop",
        "id": "m37",
        "categoryId": "beverages",
        "soldOut": false
    },
    {
        "name": "Chicken Cabe Garam",
        "price": 25000,
        "desc": "Ayam krispi bumbu cabe garam.",
        "tags": [
            "spicy",
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1623341214825-9f4f963727da?w=500&h=500&fit=crop",
        "id": "m38",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Teriyaki",
        "price": 25000,
        "desc": "Ayam teriyaki autentik dengan nasi hangat.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500&h=500&fit=crop",
        "id": "m39",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Katsu Mayo",
        "price": 25000,
        "desc": "Ayam katsu dengan saus mayo.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&h=500&fit=crop",
        "id": "m40",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Saos Asam Manis",
        "price": 25000,
        "desc": "Ayam bumbu saos asam manis.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1525755662778-989d0524087e?w=500&h=500&fit=crop",
        "id": "m41",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken BBQ",
        "price": 25000,
        "desc": "Ayam dengan bumbu BBQ berasap.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?w=500&h=500&fit=crop",
        "id": "m42",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Blackpepper",
        "price": 25000,
        "desc": "Ayam saus lada hitam khas yang pedas.",
        "tags": [
            "best",
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1544927233-a3d8b139fcb0?w=500&h=500&fit=crop",
        "id": "m43",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Sambal Matah",
        "price": 25000,
        "desc": "Ayam dengan sambal matah khas Bali.",
        "tags": [
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1626804475297-41609ea005eb?w=500&h=500&fit=crop",
        "id": "m44",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Chicken Curry Katsu",
        "price": 30000,
        "desc": "Katsu dengan kuah kari Jepang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1604908177525-4c07d3b84cb5?w=500&h=500&fit=crop",
        "id": "m45",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Dori Sambal Matah",
        "price": 30000,
        "desc": "Ikan dori lembut dengan sambal matah pedas segar.",
        "tags": [
            "best",
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=500&h=500&fit=crop",
        "id": "m46",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Dori Cabe Garam",
        "price": 30000,
        "desc": "Ikan dori krispi bumbu cabe garam.",
        "tags": [
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1623341214825-9f4f963727da?w=500&h=500&fit=crop",
        "id": "m47",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Ebi Furai Mayo",
        "price": 30000,
        "desc": "Udang goreng balut tepung panko dengan saus mayo.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1626200419188-3485ab9d71c1?w=500&h=500&fit=crop",
        "id": "m48",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Thai Beef Basil",
        "price": 35000,
        "desc": "Tumisan sapi khas Thai dengan daun kemangi yang wangi.",
        "tags": [
            "best",
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1544927233-a3d8b139fcb0?w=500&h=500&fit=crop",
        "id": "m49",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Mongolian Beef Blackpepper",
        "price": 35000,
        "desc": "Sapi bumbu lada hitam ala Mongolian.",
        "tags": [
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1544927233-a3d8b139fcb0?w=500&h=500&fit=crop",
        "id": "m50",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Smoky Beef BBQ",
        "price": 35000,
        "desc": "Daging sapi panggang dengan saus BBQ berasap.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1548943487-a2e4e43b4859?w=500&h=500&fit=crop",
        "id": "m51",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Beef Teriyaki",
        "price": 35000,
        "desc": "Sapi panggang bumbu manis gurih Jepang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1534080564583-6be75777b70a?w=500&h=500&fit=crop",
        "id": "m52",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Nasi Goreng Traditional",
        "price": 25000,
        "desc": "Nasi goreng bumbu rempah tradisional.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&h=500&fit=crop",
        "id": "m53",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Nasi Goreng Katsu",
        "price": 32000,
        "desc": "Nasi goreng disajikan dengan chicken katsu.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1604908177525-4c07d3b84cb5?w=500&h=500&fit=crop",
        "id": "m54",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Nasi Ayam Geprek",
        "price": 25000,
        "desc": "Ayam geprek pedas dengan nasi hangat.",
        "tags": [
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1626804475297-41609ea005eb?w=500&h=500&fit=crop",
        "id": "m55",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Mie Goreng Traditional",
        "price": 25000,
        "desc": "Mie goreng bumbu kecap manis gurih.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&h=500&fit=crop",
        "id": "m56",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Mie Nyemek Signature",
        "price": 30000,
        "desc": "Mie kuah kental khas Jawa.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1552611052-33e04de081de?w=500&h=500&fit=crop",
        "id": "m57",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Tori Paitan Ramen",
        "price": 28000,
        "desc": "Ramen kaldu ayam pekat.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1552611052-33e04de081de?w=500&h=500&fit=crop",
        "id": "m58",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Shoyu Ramen",
        "price": 28000,
        "desc": "Ramen kaldu kecap asin Jepang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&h=500&fit=crop",
        "id": "m59",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "TanTan Ramen",
        "price": 28000,
        "desc": "Ramen kuah wijen pedas berempah gurih.",
        "tags": [
            "spicy"
        ],
        "image": "https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=500&h=500&fit=crop",
        "id": "m60",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Katsu Ramen",
        "price": 35000,
        "desc": "Ramen khas Izakaya dengan topping chicken katsu.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=500&h=500&fit=crop",
        "id": "m61",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Beef Ramen",
        "price": 45000,
        "desc": "Ramen dengan irisan daging sapi premium.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1552611052-33e04de081de?w=500&h=500&fit=crop",
        "id": "m62",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "Bitten Ebi Ramen",
        "price": 38000,
        "desc": "Ramen dengan udang ebi furai krispi.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=500&h=500&fit=crop",
        "id": "m63",
        "categoryId": "foods",
        "soldOut": false
    },
    {
        "name": "French Fries",
        "price": 18000,
        "desc": "Kentang goreng renyah.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1576107232684-1279f390859f?w=500&h=500&fit=crop",
        "id": "m64",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Tahu Krispy",
        "price": 20000,
        "desc": "Tahu renyah potong dadu.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1544025162-81111420d6aa?w=500&h=500&fit=crop",
        "id": "m65",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Jamur Krispy",
        "price": 22000,
        "desc": "Jamur enoki / tiram goreng tepung renyah.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1544025162-81111420d6aa?w=500&h=500&fit=crop",
        "id": "m66",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Dimsum Siomay (isi 5)",
        "price": 20000,
        "desc": "Siomay kukus hangat dan lezat.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=500&h=500&fit=crop",
        "id": "m67",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Gyoza (isi 5)",
        "price": 20000,
        "desc": "Dumpling Jepang isi daging gurih.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?w=500&h=500&fit=crop",
        "id": "m68",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Cireng",
        "price": 20000,
        "desc": "Aci digoreng khas Sunda.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1544025162-81111420d6aa?w=500&h=500&fit=crop",
        "id": "m69",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Singkong Goreng",
        "price": 20000,
        "desc": "Singkong merekah gurih.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1576107232684-1279f390859f?w=500&h=500&fit=crop",
        "id": "m70",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Bitten Sampler (u/ 2 orang)",
        "price": 35000,
        "desc": "Platter ringan untuk 2 orang.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500&h=500&fit=crop",
        "id": "m71",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Bitten Platter (u/ 4 orang)",
        "price": 55000,
        "desc": "Platter besar untuk 4 orang.",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500&h=500&fit=crop",
        "id": "m72",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Pisang Lumer",
        "price": 25000,
        "desc": "Pisang goreng lumer manis.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1551024601-bec78aea704b?w=500&h=500&fit=crop",
        "id": "m73",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Classic Croffle (1 pcs)",
        "price": 12000,
        "desc": "Croffle manis renyah (1 pcs).",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1601000627763-7188737dfc1b?w=500&h=500&fit=crop",
        "id": "m74",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Classic Croffle (3 pcs)",
        "price": 30000,
        "desc": "Croffle manis renyah (3 pcs).",
        "tags": [],
        "image": "https://images.unsplash.com/photo-1601000627763-7188737dfc1b?w=500&h=500&fit=crop",
        "id": "m75",
        "categoryId": "snacks",
        "soldOut": false
    },
    {
        "name": "Bitten Waffle",
        "price": 30000,
        "desc": "Waffle dengan es krim vanilla dan saus signature.",
        "tags": [
            "best"
        ],
        "image": "https://images.unsplash.com/photo-1562376552-0d160a2f142c?w=500&h=500&fit=crop",
        "id": "m76",
        "categoryId": "snacks",
        "soldOut": false
    }
];
            this.save('bitten_menu', initialMenu);
            console.log('Database Menu Berhasil Di-generate!');
        }
    },

    // Fungsi SEEDING DUMMY ORDERS (Agar dashboard tidak kosong saat presentasi)
    initDummyOrders: function() {
        let orders = this.get('bitten_orders');
        // Hanya inject dummy order jika order sangat sedikit (misal kurang dari 10)
        if (orders.length < 10) {
            const dummyNames = ['Ariel Hikmat', 'Denis Freeman', 'Morgan Cox', 'Maja Becker', 'John Doe', 'Jane Smith', 'Budi Santoso', 'Sarah Lee'];
            const dummyMenus = ['Americano', 'Vanilla Latte', 'Caramel Latte', 'Beef Bowl', 'Chicken Katsu', 'French Fries', 'Irish Latte'];
            const dummyOrders = [];
            
            // Buat 142 pesanan selesai secara acak
            for(let i = 0; i < 142; i++) {
                const randomName = dummyNames[Math.floor(Math.random() * dummyNames.length)];
                const randomMenu1 = dummyMenus[Math.floor(Math.random() * dummyMenus.length)];
                const randomMenu2 = dummyMenus[Math.floor(Math.random() * dummyMenus.length)];
                const type = Math.random() > 0.3 ? 'Dine In' : 'Takeaway';
                const qty1 = Math.floor(Math.random() * 3) + 1;
                const qty2 = Math.floor(Math.random() * 2) + 1;
                
                dummyOrders.push({
                    id: Math.floor(Math.random() * 10000).toString(),
                    table: type === 'Dine In' ? '0' + (Math.floor(Math.random() * 6) + 1) : 'TA',
                    customer: randomName,
                    type: type,
                    status: 'Completed',
                    time: '12:' + (Math.floor(Math.random() * 50) + 10) + ' PM',
                    items: [
                        { name: randomMenu1, qty: qty1, price: 25000 },
                        { name: randomMenu2, qty: qty2, price: 20000 }
                    ],
                    total: (qty1 * 25000) + (qty2 * 20000)
                });
            }
            
            // Gabungkan dengan pesanan yang sudah ada (biarkan pesanan asli tetap di atas)
            this.save('bitten_orders', [...orders, ...dummyOrders]);
            console.log("Dummy Orders berhasil di-generate!");
        }
    },

    // 4. CRUD MENU
    addMenu: function(newMenuData) {
        let menus = this.get('bitten_menu');
        // Generate ID sederhana
        newMenuData.id = 'm' + (menus.length > 0 ? Math.max(...menus.map(m => parseInt(m.id.replace('m','')))) + 1 : 1);
        menus.push(newMenuData);
        this.save('bitten_menu', menus);
        return newMenuData;
    },
    
    toggleSoldOut: function(menuId) {
        let menus = this.get('bitten_menu');
        let menuIndex = menus.findIndex(m => m.id === menuId);
        if(menuIndex !== -1) {
            menus[menuIndex].soldOut = !menus[menuIndex].soldOut;
            this.save('bitten_menu', menus);
        }
    },

    // 5. CRUD ORDER
    createOrder: function(tableNumber, customerName, orderType, cartItems, total) {
        let orders = this.get('bitten_orders');
        let newOrder = {
            id: Math.floor(Math.random() * 10000).toString(), // Format singkat
            table: tableNumber,
            customer: customerName,
            type: orderType,
            status: 'Masuk',
            items: cartItems,
            total: total,
            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        };
        // Masukkan di urutan pertama (paling baru)
        orders.unshift(newOrder);
        this.save('bitten_orders', orders);
        return newOrder.id;
    },

    updateOrderStatus: function(orderId, newStatus) {
        let orders = this.get('bitten_orders');
        let orderIndex = orders.findIndex(o => o.id === orderId || o.id === orderId.toString());
        if(orderIndex !== -1) {
            orders[orderIndex].status = newStatus;
            this.save('bitten_orders', orders);
        }
    }
};

// Inisialisasi Database saat dipanggil
DB.initMenu();

window.printQRWindow = function(table) {
    const printWindow = window.open("", "", "width=400,height=500");
    if (!printWindow) return;
    printWindow.document.open();
    printWindow.document.write("<!DOCTYPE html><html><head><title>Print QR</title></head><body style=\"text-align:center; font-family:Arial, sans-serif; margin-top:50px;\"><h2 id=\"title\" style=\"font-size:24px; margin-bottom: 20px;\"></h2><img id=\"qr\" style=\"width:250px; height:250px; display:block; margin: 0 auto;\" /><p style=\"font-size:14px; color:#555; margin-top: 20px;\">Bitten Coffee - Scan to Order</p></body></html>");
    printWindow.document.getElementById("title").textContent = table.id;
    printWindow.document.getElementById("qr").src = table.qr;
    const script = printWindow.document.createElement("script");
    script.textContent = "window.onload = () => { window.print(); setTimeout(()=>window.close(), 500); }";
    printWindow.document.body.appendChild(script);
    printWindow.document.close();
};
