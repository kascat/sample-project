import components from 'components'
import { boot } from 'quasar/wrappers'

let randomCount = 1
export default boot(({ app }) => {
  for (const component of components) {
    const name = component.name || `RandomComponent${randomCount}`
    app.component(name, component)
    if (name.startsWith('RandomComponent')) {
      randomCount++
    }
  }
})
