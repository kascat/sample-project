import faker from 'faker'

const sales = []
for (let i = 0; i < 10; i++) {
  sales.push({
    date: faker.date.past(),
    items: [
      {
        category: faker.commerce.department(),
        color: faker.internet.color(),
        quantity: faker.helpers.randomize([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
      },
      {
        category: faker.commerce.department(),
        color: faker.internet.color(),
        quantity: faker.helpers.randomize([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
      },
      {
        category: faker.commerce.department(),
        color: faker.internet.color(),
        quantity: faker.helpers.randomize([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
      }
    ]
  })
}

export default sales
