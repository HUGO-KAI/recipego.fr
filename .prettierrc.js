module.exports = {
  plugins: ["prettier-plugin-twig-melody"],
  overrides: [
    {
      files: "*.twig",
      options: {
        printWidth: 100, // Adjust this value as per your needs
        // Add any other Prettier options you want to customize
      },
    },
  ],
};
