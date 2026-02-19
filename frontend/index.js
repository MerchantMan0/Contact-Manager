import { ContactTable } from "./table.js";

if (!getCookie("token")) {
  // Users have to login
  window.location.href = "/login.html"
}


/** @type {Contact[]} */
const placeholderContacts = [
  {
    id: "1",
    name: "Alice",
    email: "a.lice@gmail.com",
    phone: "(555) 555-5555",
    created: "unknown",
  },
  {
    id: "2",
    name: "Bob",
    email: "b.o.b@bob.bob",
    phone: "(bob) bob-bobb",
    created: "bob",
  },
];

const table = new ContactTable(document.getElementById("contact-table"));
//table.addContact(placeholderContacts[0]);
//table.addContact(placeholderContacts[1]);
//table.display();

const addButton = document.getElementById("add-contact");
addButton.onclick = function () {
  table.makeCreationRow();
};

/**
 *
 * @param {string} text Text to find
 * @param {import("./table.js").Contact} contact The contact to search
 * @returns {boolean}
 */
function hasTextSomewhere(text, contact) {
  return (
    contact.name.includes(text) ||
    contact.email.includes(text) ||
    contact.phone.includes(text) ||
    contact.created?.toString?.()?.includes?.(text)
  );
}

// Set up the search bar
const searchBar = document.getElementById("search-bar");
searchBar.addEventListener("input", (event) => {
  table.setFilter((contact) => hasTextSomewhere(event.target.value, contact));
  table.display();
});


// fetch stuff at the end in case it breaks
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

fetch(window.location.origin + "/api/contacts/getContacts.php", {
  method: "GET",
	headers: {
		"Authorization": `Bearer ${getCookie("token")}`
	}
}).then(data => data.text()).then(text => JSON.parse(text.substring(1))).then(json => json.contacts.map(contact => table.addContact(contact))).then(() => table.display())

