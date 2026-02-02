import { ContactTable } from "./table.js";

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
table.addContact(placeholderContacts[0]);
table.addContact(placeholderContacts[1]);
table.display();

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
    contact.created.toString().includes(text)
  );
}

// Set up the search bar
const searchBar = document.getElementById("search-input");
searchBar.addEventListener("input", (event) => {
  console.log(event.target.value);
});
