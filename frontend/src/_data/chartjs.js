import sales from './db/sales'

const labels = Object.keys(
  sales.reduce((l, s) => {
    const c = s.items.map(i => i.category)
    for (const _c of c) {
      if (!l[_c]) l[_c] = true
    }
    return l
  }, {})
)

const byCategory = {
  label: '#Departments',
  data: [],
  backgroundColor: [],
  borderColor: [],
  borderWidth: 1
}

for (const sale of sales) {
  for (const item of sale.items) {
    const index = labels.indexOf(item.category)
    if (!byCategory.data[index]) {
      byCategory.data[index] = 0
    }
    byCategory.data[index] += item.quantity
    if (!byCategory.backgroundColor[index]) {
      byCategory.backgroundColor[index] = `${item.color}66`
      byCategory.borderColor[index] = item.color
    }
  }
}

export {
  labels,
  byCategory
}
