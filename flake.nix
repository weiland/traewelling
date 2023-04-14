{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-parts.url = "github:hercules-ci/flake-parts";
    nix-npm-buildpackage.url = "github:serokell/nix-npm-buildpackage";
  };

  outputs = inputs@{ flake-parts, ... }:
    flake-parts.lib.mkFlake { inherit inputs; } {
      systems = [ "x86_64-linux" "aarch64-darwin" ];

      perSystem = { system, config, pkgs, lib, ... }:
        {
          _module.args.pkgs = import inputs.nixpkgs {
            inherit system;
            overlays = [ inputs.nix-npm-buildpackage.overlays.default ];
          };

          packages =
            let
              inherit (builtins) substring;

              mtime = inputs.self.lastModifiedDate;
              date = "${substring 0 4 mtime}-${substring 4 2 mtime}-${substring 6 2 mtime}";
              src = lib.cleanSource ./.;
            in
            rec {
              node = pkgs.buildNpmPackage {
                inherit src;
                npmBuild = "npm run production";
              };

              php = pkgs.callPackage ./composer-project.nix { } src;

              default = php.overrideAttrs (oldAttrs:
                {
                  pname = "traewelling";
                  version = "unstable-${date}";
                  postInstall = ''
                    # replace public folder with node build artifacts
                    rm -rf $out/libexec/source/public
                    cp -r ${node}/public $out/libexec/public
                  '';
                });
            };
        };
    };
}
