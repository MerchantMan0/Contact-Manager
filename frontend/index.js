/**
 * @typedef {Object} Contact
 * @property {string} id
 * @property {string} name
 * @property {string} email
 * @property {string} phone
 * @property {unknown} created
 */

/**
 * Managing the table through a class rather than directly
 */
class ContactTable {
  /**
   *
   * @param {HTMLTableElement} element The table to modify
   * @param {Contact[]} [contacts=[]]
   */
  constructor(element, contacts = []) {
    /** @type {HTMLTableElement} */
    this.element = element;

    /** @type {Contact[]} */
    this.contacts = contacts;
  }

  /**
   * @param {Contact} contact
   */
  addContact(contact) {
    this.contacts.push(contact);
  }

  /**
   * @param {string} id
   */
  removeContact(id) {
    this.contacts.filter((contact) => contact.id !== id);
  }

  /**
   * Update the table with the current contacts, optionally with a condition
   * @param {(contact: Contact) => boolean} [predicate] A function to decide if the contact should be shown
   */
  display(predicate) {
    function makeRow(contact) {
      const row = document.createElement("tr");
      for (const field of ["name", "email", "phone", "created"]) {
        const cell = row.insertCell();
        cell.innerHTML = contact[field];
      }
      return row;
    }

    const rows = this.contacts.filter(predicate || (() => true)).map(makeRow);

    console.log(rows);
    console.log(this.element.tBodies[0].replaceChildren(...rows));
  }

  /**
   * Remove all shown contacts from the table
   */
  clear() {
    this.element.tBodies[0].replaceChildren();
  }
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
