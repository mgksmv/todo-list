export function enumToOptions<
  T extends string,
  E extends Record<string, T>,
  L extends Record<T, string>,
>(enumObj: E, labels: L) {
  return Object.values(enumObj).map((value) => ({
    label: labels[value],
    value,
  }));
}
