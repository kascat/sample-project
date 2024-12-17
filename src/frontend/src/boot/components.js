import components from '../components';
import { boot } from 'quasar/wrappers';

export default boot(({ app }) => {
  for (const componentName of Object.keys(components)) {
    app.component(componentName, components[componentName]);
  }
});
